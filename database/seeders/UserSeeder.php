<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $pwd = Hash::make('123456');
        User::insert([
            ['tenant_id' => null, 'name' => 'Admin Plataforma', 'email' => 'admin@plataforma.pt', 'password' => $pwd, 'role' => 'super_admin', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Gerente Barbearia', 'email' => 'gerente@barbearia-classica.pt', 'password' => $pwd, 'role' => 'manager', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Cliente Teste', 'email' => 'cliente@teste.pt', 'password' => $pwd, 'role' => 'customer', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'name' => 'Admin Barbearia', 'email' => 'admin@barbearia-classica.pt', 'password' => $pwd, 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
