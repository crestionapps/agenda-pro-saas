<?php
namespace App\Http\Controllers;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Appointment;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function businessDashboard($tenantId)
    {
        $tenant = Tenant::withCount(['professionals', 'services', 'appointments', 'reviews'])->findOrFail($tenantId);
        $todayAppointments = Appointment::where('tenant_id', $tenantId)
            ->where('date', now()->format('Y-m-d'))
            ->with(['customer', 'professional', 'service'])
            ->orderBy('start_time')
            ->get();
        $professionals = $tenant->professionals()->with('services')->get();
        $services = $tenant->services()->with('professionals')->get();
        return view('dashboard.business', compact('tenant', 'todayAppointments', 'professionals', 'services', 'tenantId'));
    }

    public function platformDashboard()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_users' => User::count(),
            'total_appointments' => Appointment::count(),
            'appointments_today' => Appointment::where('date', now()->format('Y-m-d'))->count(),
            'active_tenants' => Tenant::where('is_active', true)->count(),
        ];
        $topTenants = Tenant::withCount('appointments')->orderBy('appointments_count', 'desc')->take(10)->get();
        $recentAppointments = Appointment::with(['tenant', 'customer', 'service'])
            ->latest()->take(20)->get();
        return view('dashboard.platform', compact('stats', 'topTenants', 'recentAppointments'));
    }

    public function tenants()
    {
        $tenants = Tenant::withCount(['appointments', 'professionals'])->latest()->get();
        return view('dashboard.tenants', compact('tenants'));
    }

    public function createTenant(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tenants',
            'type' => 'required|in:barbershop,hairdresser,spa,nails,aesthetics,clinic',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'city' => 'nullable|string',
        ]);
        Tenant::create($data);
        return back()->with('success', 'Negócio criado');
    }

    public function toggleTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['is_active' => !$tenant->is_active]);
        return back()->with('success', 'Estado atualizado');
    }
}
