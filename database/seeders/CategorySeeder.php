<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Action Figures', 'description' => 'Bonecos e figuras colecionáveis']);
        Category::create(['name' => 'Cartas', 'description' => 'Cards e cartas colecionáveis']);
        Category::create(['name' => 'Quadrinhos', 'description' => 'HQs e mangás']);
        Category::create(['name' => 'Miniaturas', 'description' => 'Miniaturas colecionáveis']);
        Category::create(['name' => 'Jogos', 'description' => 'Jogos de tabuleiro e RPG']);
    }
}