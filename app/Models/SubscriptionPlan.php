<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'price', 'sms_quota', 'email_quota', 'whatsapp_quota', 'is_active'];

    public function subscriptions() { return $this->hasMany(TenantSubscription::class, 'plan_id'); }
}
