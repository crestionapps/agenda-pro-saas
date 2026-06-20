<?php
namespace App\Http\Controllers;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::all();
        return view('dashboard.plans', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'sms_quota' => 'integer|min:0',
            'email_quota' => 'integer|min:0',
            'whatsapp_quota' => 'integer|min:0',
        ]);
        SubscriptionPlan::create($data);
        return back()->with('success', 'Plano criado');
    }
}
