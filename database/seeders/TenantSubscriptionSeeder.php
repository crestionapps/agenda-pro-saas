<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\TenantSubscription;
class TenantSubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        TenantSubscription::insert([
            ['tenant_id' => 1, 'plan_id' => 1, 'start_date' => now(), 'is_active' => true, 'sms_remaining' => 50, 'email_remaining' => 100, 'whatsapp_remaining' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'plan_id' => 2, 'start_date' => now(), 'is_active' => true, 'sms_remaining' => 200, 'email_remaining' => 500, 'whatsapp_remaining' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'plan_id' => 1, 'start_date' => now(), 'is_active' => true, 'sms_remaining' => 50, 'email_remaining' => 100, 'whatsapp_remaining' => 30, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
