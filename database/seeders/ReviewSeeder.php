<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Review;
class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::insert([
            ['tenant_id' => 1, 'customer_id' => 3, 'rating' => 5, 'comment' => 'Excelente serviço! O Carlos é incrível.', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'customer_id' => 3, 'rating' => 4, 'comment' => 'Muito bom, mas tempo de espera um pouco longo.', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'customer_id' => 3, 'rating' => 5, 'comment' => 'Melhor SPA de Lisboa. Super recomendo!', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 3, 'customer_id' => 3, 'rating' => 4, 'comment' => 'Adorei o resultado. A Inês é muito talentosa.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
