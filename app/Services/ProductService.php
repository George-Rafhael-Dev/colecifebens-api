<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function getAll(): mixed
    {
        return Product::all();
    }

    public function getById(int $id): Product
    {
        $product = Product::find($id);
        if (!$product) throw new \Exception('Product not found', 404);
        return $product;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->getById($id);
        $product->update($data);
        return $product->fresh();
    }

    public function delete(int $id): array
    {
        $product = $this->getById($id);
        $product->delete();
        return ['message' => 'Product deleted'];
    }
}