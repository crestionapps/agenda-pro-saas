<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Professional;
class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        Professional::insert([
            ['tenant_id' => 1, 'name' => 'Carlos Silva', 'email' => 'carlos@barbearia.pt', 'phone' => '+351 910 000 001', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Miguel Santos', 'email' => 'miguel@barbearia.pt', 'phone' => '+351 910 000 002', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'João Pereira', 'email' => 'joao@barbearia.pt', 'phone' => '+351 910 000 003', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'Ana Martins', 'email' => 'ana@spazen.pt', 'phone' => '+351 920 000 001', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'Sofia Costa', 'email' => 'sofia@spazen.pt', 'phone' => '+351 920 000 002', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'name' => 'Rita Oliveira', 'email' => 'rita@spazen.pt', 'phone' => '+351 920 000 003', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Inês Rodrigues', 'email' => 'ines@elegance.pt', 'phone' => '+351 930 000 001', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Maria Fernandes', 'email' => 'maria@elegance.pt', 'phone' => '+351 930 000 002', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'name' => 'Beatriz Almeida', 'email' => 'beatriz@elegance.pt', 'phone' => '+351 930 000 003', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
