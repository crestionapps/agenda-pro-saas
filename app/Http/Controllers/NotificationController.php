<?php
namespace App\Http\Controllers;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index($tenantId)
    {
        $notifications = NotificationLog::where('tenant_id', $tenantId)->latest()->paginate(20);
        return view('dashboard.notifications', compact('notifications', 'tenantId'));
    }
}
