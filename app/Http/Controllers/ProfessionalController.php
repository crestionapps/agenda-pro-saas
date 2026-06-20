<?php
namespace App\Http\Controllers;
use App\Models\Professional;
use App\Models\BusinessHour;
use Illuminate\Http\Request;

class ProfessionalController extends Controller
{
    public function apiByTenant($slug)
    {
        $tenant = \App\Models\Tenant::where('slug', $slug)->firstOrFail();
        return response()->json($tenant->professionals()->with('services')->get());
    }

    public function index($tenantId)
    {
        $professionals = Professional::where('tenant_id', $tenantId)->with('services')->get();
        return view('dashboard.business-professionals', compact('professionals', 'tenantId'));
    }

    public function store(Request $request, $tenantId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);
        $data['tenant_id'] = $tenantId;
        $professional = Professional::create($data);
        if ($request->service_ids) {
            $professional->services()->sync($request->service_ids);
        }
        return back()->with('success', 'Profissional criado');
    }

    public function update(Request $request, $tenantId, $id)
    {
        $professional = Professional::where('tenant_id', $tenantId)->findOrFail($id);
        $professional->update($request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
        ]));
        if ($request->has('service_ids')) {
            $professional->services()->sync($request->service_ids);
        }
        return back()->with('success', 'Profissional atualizado');
    }

    public function destroy($tenantId, $id)
    {
        Professional::where('tenant_id', $tenantId)->findOrFail($id)->delete();
        return back()->with('success', 'Profissional removido');
    }

    public function updateHours(Request $request, $tenantId)
    {
        $data = $request->validate([
            'hours' => 'required|array',
            'hours.*.day_of_week' => 'required|integer|between:0,6',
            'hours.*.open_time' => 'nullable|string',
            'hours.*.close_time' => 'nullable|string',
            'hours.*.is_open' => 'boolean',
            'professional_id' => 'nullable|exists:professionals,id',
        ]);
        $professionalId = $request->professional_id;
        BusinessHour::where('tenant_id', $tenantId)
            ->when($professionalId, fn($q) => $q->where('professional_id', $professionalId))
            ->delete();
        foreach ($data['hours'] as $hour) {
            BusinessHour::create([
                'tenant_id' => $tenantId,
                'professional_id' => $professionalId,
                'day_of_week' => $hour['day_of_week'],
                'open_time' => $hour['is_open'] ? $hour['open_time'] : null,
                'close_time' => $hour['is_open'] ? $hour['close_time'] : null,
                'is_open' => $hour['is_open'] ?? true,
            ]);
        }
        return back()->with('success', 'Horários atualizados');
    }
}
