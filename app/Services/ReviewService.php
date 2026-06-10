<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Review;

class ReviewService
{
    public function getAll(): mixed
    {
        return Review::all();
    }

    public function getById(int $id): Review
    {
        $review = Review::find($id);
        if (!$review) throw new \Exception('Review not found', 404);
        return $review;
    }

    public function create(array $data): Review
    {
        $purchased = Order::where('user_id', $data['user_id'])
            ->where('status', 'entregue')
            ->where('payment_status', 'aprovado')
            ->whereHas('products', fn($q) => $q->where('products.id', $data['product_id']))
            ->exists();

        if (!$purchased) {
            throw new \Exception('You can only review products you have purchased and received', 422);
        }

        if (Review::where('user_id', $data['user_id'])->where('product_id', $data['product_id'])->exists()) {
            throw new \Exception('User already reviewed this product', 422);
        }

        return Review::create($data);
    }

    public function delete(int $id): array
    {
        $review = $this->getById($id);
        $review->delete();
        return ['message' => 'Review deleted'];
    }
}