<?php
namespace App\Http\Controllers;
use App\Models\Appointment;
use App\Models\Tenant;
use App\Models\Professional;
use App\Models\Service;
use App\Models\BusinessHour;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function booking($slug)
    {
        $tenant = Tenant::where('slug', $slug)
            ->with(['services.professionals', 'professionals'])
            ->firstOrFail();
        return view('booking.index', compact('tenant'));
    }

    public function slots(Request $request, $slug, $professionalId)
    {
        $tenant = Tenant::where('slug', $slug)->firstOrFail();
        $professional = Professional::where('tenant_id', $tenant->id)->findOrFail($professionalId);
        $date = $request->date;
        if (!$date) return response()->json([]);

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $hours = BusinessHour::where('tenant_id', $tenant->id)
            ->where('professional_id', $professionalId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_open', true)
            ->first();

        if (!$hours) return response()->json([]);

        $serviceId = $request->service_id;
        $duration = 60;
        if ($serviceId) {
            $service = Service::find($serviceId);
            $duration = $service ? $service->duration : 60;
        }

        $existingAppointments = Appointment::where('professional_id', $professionalId)
            ->where('date', $date)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->get();

        $slots = [];
        $start = Carbon::parse($hours->open_time);
        $end = Carbon::parse($hours->close_time);

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);
            $available = true;
            foreach ($existingAppointments as $apt) {
                $aptStart = Carbon::parse($apt->start_time);
                $aptEnd = Carbon::parse($apt->end_time);
                if ($start->lt($aptEnd) && $slotEnd->gt($aptStart)) {
                    $available = false;
                    break;
                }
            }
            if ($available) {
                $slots[] = $start->format('H:i');
            }
            $start->addMinutes($duration);
        }

        return response()->json($slots);
    }

    public function verifySlot(Request $request)
    {
        $request->validate([
            'professional_id' => 'required|exists:professionals,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        $available = Appointment::isSlotAvailable(
            $request->professional_id,
            $request->date,
            $request->start_time,
            $request->end_time
        );
        return response()->json(['available' => $available]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'service_id' => 'required|exists:services,id',
            'professional_id' => 'required|exists:professionals,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $service = Service::findOrFail($data['service_id']);
        $data['end_time'] = Carbon::parse($data['start_time'])->addMinutes($service->duration)->format('H:i');

        if (!Appointment::isSlotAvailable($data['professional_id'], $data['date'], $data['start_time'], $data['end_time'])) {
            return back()->withErrors(['slot' => 'Este horário já não está disponível']);
        }

        $data['customer_id'] = auth()->id();
        $data['status'] = 'scheduled';

        Appointment::create($data);
        return redirect()->route('customer.dashboard')->with('success', 'Agendamento confirmado!');
    }

    public function updateStatus(Request $request, $tenantId, $id)
    {
        $request->validate(['status' => 'required|in:scheduled,confirmed,cancelled,completed,no_show']);
        $appointment = Appointment::where('tenant_id', $tenantId)->findOrFail($id);
        $appointment->update(['status' => $request->status]);
        return back()->with('success', 'Estado atualizado');
    }

    public function cancel(Request $request, $id)
    {
        $appointment = Appointment::where('customer_id', auth()->id())->findOrFail($id);
        $appointment->update(['status' => 'cancelled']);
        return back()->with('success', 'Agendamento cancelado');
    }

    public function managerDashboard($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $date = request('date', now()->format('Y-m-d'));
        $appointments = Appointment::where('tenant_id', $tenantId)
            ->where('date', $date)
            ->with(['customer', 'service', 'professional'])
            ->orderBy('start_time')
            ->get();
        return view('dashboard.manager', compact('tenant', 'appointments', 'date'));
    }
}
