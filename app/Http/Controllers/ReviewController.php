<?php

namespace App\Http\Controllers;

use App\Http\Services\ReviewService;
use App\Http\Requests\StoreReviewRequest;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $service) {}
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
        return response()->json($this->service->getAll());    }
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
        try {
            return response()->json($this->service->getById($id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
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
    public function store(StoreReviewRequest $request)
    {
        try {
            return response()->json($this->service->create($request->validated()), 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
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
        try {
            return response()->json($this->service->delete($id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }
}