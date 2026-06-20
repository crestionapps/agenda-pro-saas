<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (\App\Models\Tenant::count() > 0) {
            $this->command->info('Base de dados já tem dados. Skipping seed.');
            return;
        }
        $this->call([
            SubscriptionPlanSeeder::class,
            TenantSeeder::class,
            UserSeeder::class,
            ProfessionalSeeder::class,
            ServiceSeeder::class,
            ServiceProfessionalSeeder::class,
            BusinessHourSeeder::class,
            TenantSubscriptionSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
