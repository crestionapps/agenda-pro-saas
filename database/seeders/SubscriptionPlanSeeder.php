<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        SubscriptionPlan::insert([
            ['name' => 'Basic', 'price' => 19.90, 'sms_quota' => 50, 'email_quota' => 100, 'whatsapp_quota' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pro', 'price' => 49.90, 'sms_quota' => 200, 'email_quota' => 500, 'whatsapp_quota' => 100, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Enterprise', 'price' => 99.90, 'sms_quota' => 1000, 'email_quota' => 2000, 'whatsapp_quota' => 500, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
