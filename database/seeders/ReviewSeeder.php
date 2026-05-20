<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::create([
            'user_id'    => 2,
            'product_id' => 1,
            'rating'     => 5,
            'comment'    => 'Produto excelente, chegou rápido e bem embalado!',
        ]);
    }
}