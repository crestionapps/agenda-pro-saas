<?php
namespace App\Http\Controllers;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function home()
    {
        $categories = ['barbershop' => 'Barbearias', 'hairdresser' => 'Cabeleireiros', 'spa' => 'SPA', 'nails' => 'Unhas', 'aesthetics' => 'Estética', 'clinic' => 'Clínicas'];
        $tenants = Tenant::where('is_active', true)->withCount('reviews')->get();
        $cities = Tenant::where('is_active', true)->whereNotNull('city')->select('city')->distinct()->pluck('city');
        return view('home', compact('tenants', 'categories', 'cities'));
    }

    public function show($slug)
    {
        $tenant = Tenant::where('slug', $slug)
            ->with(['services.professionals', 'professionals', 'businessHours', 'reviews.customer'])
            ->firstOrFail();
        return view('tenant.show', compact('tenant'));
    }

    public function apiShow($slug)
    {
        $tenant = Tenant::where('slug', $slug)
            ->with(['services.professionals', 'professionals', 'businessHours', 'reviews.customer'])
            ->firstOrFail();
        return response()->json($tenant);
    }

    public function apiSearch(Request $request)
    {
        $query = Tenant::where('is_active', true);
        if ($request->q) $query->where(function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->q}%")->orWhere('city', 'like', "%{$request->q}%");
        });
        if ($request->category) $query->where('type', $request->category);
        if ($request->city) $query->where('city', $request->city);
        return response()->json($query->withCount('reviews')->get());
    }
}
