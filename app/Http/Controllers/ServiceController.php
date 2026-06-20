<?php
namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function apiByTenant($slug)
    {
        $tenant = Tenant::where('slug', $slug)->firstOrFail();
        return response()->json($tenant->services()->with('professionals')->get());
    }

    public function index($tenantId)
    {
        $services = Service::where('tenant_id', $tenantId)->with('professionals')->get();
        $professionals = \App\Models\Professional::where('tenant_id', $tenantId)->get();
        return view('dashboard.business-services', compact('services', 'professionals', 'tenantId'));
    }

    public function store(Request $request, $tenantId)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:5',
            'category' => 'nullable|string',
        ]);
        $data['tenant_id'] = $tenantId;
        $service = Service::create($data);
        if ($request->professional_ids) {
            $service->professionals()->sync($request->professional_ids);
        }
        return back()->with('success', 'Serviço criado');
    }

    public function update(Request $request, $tenantId, $id)
    {
        $service = Service::where('tenant_id', $tenantId)->findOrFail($id);
        $service->update($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:5',
            'category' => 'nullable|string',
        ]));
        if ($request->has('professional_ids')) {
            $service->professionals()->sync($request->professional_ids);
        }
        return back()->with('success', 'Serviço atualizado');
    }

    public function destroy($tenantId, $id)
    {
        Service::where('tenant_id', $tenantId)->findOrFail($id)->delete();
        return back()->with('success', 'Serviço removido');
    }
}
