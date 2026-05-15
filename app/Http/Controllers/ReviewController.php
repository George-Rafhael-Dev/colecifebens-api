<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        return response()->json(Review::all());
    }

    public function show(int $id)
    {
        $review = Review::find($id);

        if (!$review) return response()->json(['message' => 'Review not found'], 404);

        return response()->json($review);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'    => 'required|integer|exists:users,id',
            'product_id' => 'required|integer|exists:products,id',
            'rating'     => 'required|integer|between:1,5',
            'comment'    => 'nullable|string',
        ]);

        $errors = [];

        $purchased = Order::where('user_id', $request->user_id)
            ->where('status', 'entregue')
            ->where('payment_status', 'aprovado')
            ->whereHas('products', function ($query) use ($request) {
                $query->where('products.id', $request->product_id);
            })
            ->exists();

        if (!$purchased) {
            $errors['product_id'] = ['You can only review products you have purchased and received'];
        }

        $exists = Review::where('user_id', $request->user_id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            $errors['review'] = ['User already reviewed this product'];
        }

        if (!empty($errors)) {
            return response()->json(['message' => 'Validation failed', 'errors' => $errors], 422);
        }

        $review = Review::create($request->only([
            'user_id', 'product_id', 'rating', 'comment',
        ]));

        return response()->json($review, 201);
    }

    public function destroy(int $id)
    {
        $review = Review::find($id);

        if (!$review) return response()->json(['message' => 'Review not found'], 404);

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}