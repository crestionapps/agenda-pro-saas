<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TenantSubscription extends Model
{
    protected $fillable = ['tenant_id', 'plan_id', 'start_date', 'end_date', 'is_active', 'sms_remaining', 'email_remaining', 'whatsapp_remaining'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function plan() { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }
}
