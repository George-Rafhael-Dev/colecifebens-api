<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name'        => 'Goku Super Sayajin',
            'description' => 'Action figure raro do Goku',
            'price'       => 350.00,
            'rarity'      => 'raro',
            'condition'   => 'novo',
            'stock'       => 5,
            'user_id'     => 1,
            'category_id' => 1,
        ]);

        Product::create([
            'name'        => 'Carta Charizard Holo',
            'description' => 'Carta holográfica rara do Charizard',
            'price'       => 800.00,
            'rarity'      => 'muito raro',
            'condition'   => 'usado',
            'stock'       => 2,
            'user_id'     => 2,
            'category_id' => 2,
        ]);

        Product::create([
            'name'        => 'Batman Ano Um',
            'description' => 'HQ clássica do Batman',
            'price'       => 120.00,
            'rarity'      => 'comum',
            'condition'   => 'usado',
            'stock'       => 3,
            'user_id'     => 1,
            'category_id' => 3,
        ]);
    }
}