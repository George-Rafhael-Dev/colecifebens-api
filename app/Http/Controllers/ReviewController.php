<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/reviews",
     *     summary="List all reviews",
     *     tags={"Reviews"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return response()->json(Review::all());
    }
    /**
     * @OA\Get(
     *     path="/reviews/{id}",
     *     summary="Show review",
     *     tags={"Reviews"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id)
    {
        $review = Review::find($id);

        if (!$review) return response()->json(['message' => 'Review not found'], 404);

        return response()->json($review);
    }
    /**
     * @OA\Post(
     *     path="/reviews",
     *     summary="Create review",
     *     tags={"Reviews"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","product_id","rating"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="comment", type="string", example="Produto excelente!")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error or business rule violation")
     * )
     */
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
    /**
     * @OA\Delete(
     *     path="/reviews/{id}",
     *     summary="Delete review",
     *     tags={"Reviews"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id)
    {
        $review = Review::find($id);

        if (!$review) return response()->json(['message' => 'Review not found'], 404);

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}