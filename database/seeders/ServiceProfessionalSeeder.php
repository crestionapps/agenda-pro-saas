<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ServiceProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('service_professional')->insert([
            [1,1],[1,2],[1,3],[2,1],[2,2],[3,1],[3,2],[4,1],
            [5,4],[5,5],[6,4],[6,6],[7,4],[7,5],[7,6],[8,5],[8,6],
            [9,7],[9,8],[10,7],[10,8],[11,7],[11,8],[11,9],[12,7],[12,9],
        ]);
    }
}
