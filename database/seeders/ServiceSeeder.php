<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Service;
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::insert([
            ['tenant_id' => 1, 'name' => 'Corte de Cabelo', 'price' => 15.00, 'duration' => 30, 'category' => 'Corte', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Barba Completa', 'price' => 10.00, 'duration' => 20, 'category' => 'Barba', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Corte + Barba', 'price' => 22.00, 'duration' => 45, 'category' => 'Combo', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Hot Towel Shave', 'price' => 18.00, 'duration' => 30, 'category' => 'Barba', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'Massagem Relaxante', 'price' => 45.00, 'duration' => 60, 'category' => 'Massagem', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'Facial Premium', 'price' => 55.00, 'duration' => 75, 'category' => 'Facial', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'SPA Day', 'price' => 120.00, 'duration' => 180, 'category' => 'Tratamento', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'Manicure', 'price' => 30.00, 'duration' => 45, 'category' => 'Mãos', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Corte e Escovagem', 'price' => 35.00, 'duration' => 45, 'category' => 'Corte', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Coloração', 'price' => 55.00, 'duration' => 90, 'category' => 'Cor', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Tratamento Capilar', 'price' => 40.00, 'duration' => 60, 'category' => 'Tratamento', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Design de Sobrancelhas', 'price' => 20.00, 'duration' => 30, 'category' => 'Design', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
