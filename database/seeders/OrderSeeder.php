<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $product1 = Product::find(1);
        $product2 = Product::find(2);

        $order1 = Order::create([
            'user_id'        => 2,
            'total'          => $product1->price * 2,
            'status'         => 'entregue',
            'payment_status' => 'aprovado',
            'payment_method' => 'pix',
            'paid_at'        => now(),
        ]);

        $order1->products()->attach([
            $product1->id => [
                'quantity'   => 2,
                'unit_price' => $product1->price,
            ],
        ]);

        $product1->decrement('stock', 2);

        $order2 = Order::create([
            'user_id'        => 1,
            'total'          => $product2->price,
            'status'         => 'pendente',
            'payment_status' => 'aguardando',
            'payment_method' => 'boleto',
        ]);

        $order2->products()->attach([
            $product2->id => [
                'quantity'   => 1,
                'unit_price' => $product2->price,
            ],
        ]);

        $product2->decrement('stock', 1);
    }
}