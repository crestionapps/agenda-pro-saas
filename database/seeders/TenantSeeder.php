<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::insert([
            ['name' => 'Barbearia Clássica', 'slug' => 'barbearia-classica', 'type' => 'barbershop', 'description' => 'Barbearia tradicional com serviços modernos', 'address' => 'Rua Augusta 24, Lisboa', 'phone' => '+351 210 000 001', 'email' => 'info@barbeariaclassica.pt', 'city' => 'Lisboa', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'SPA Zen Retreat', 'slug' => 'spa-zen-retreat', 'type' => 'spa', 'description' => 'Relaxamento e bem-estar no coração da cidade', 'address' => 'Av. da Liberdade 150, Lisboa', 'phone' => '+351 210 000 002', 'email' => 'info@spazen.pt', 'city' => 'Lisboa', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salon Elegance', 'slug' => 'salon-elegance', 'type' => 'hairdresser', 'description' => 'Cabeleireiro de luxo com tendências mundiais', 'address' => 'Rua das Flores 45, Porto', 'phone' => '+351 220 000 003', 'email' => 'info@salonelegance.pt', 'city' => 'Porto', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
