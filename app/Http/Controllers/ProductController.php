<?php

namespace App\Http\Controllers;

use App\Http\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function __construct(private ProductService $service) {}
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="List all products",
     *     tags={"Products"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return response()->json($this->service->getAll());
    }
    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Show product",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id)
    {
        try {
            $retorno = $this->service->getById($id);
            dd($Product);
            return response()->json($this->service->getById($id));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","condition","stock","user_id","category_id"},
     *             @OA\Property(property="name", type="string", example="Goku Super Sayajin"),
     *             @OA\Property(property="description", type="string", example="Action figure raro"),
     *             @OA\Property(property="price", type="number", example=350.00),
     *             @OA\Property(property="rarity", type="string", example="raro"),
     *             @OA\Property(property="condition", type="string", enum={"novo","usado","restaurado"}, example="novo"),
     *             @OA\Property(property="stock", type="integer", example=3),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        return response()->json($this->service->create($request->validated()), 201);
    }
    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update product (all fields)",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","price","condition","stock","category_id"},
     *             @OA\Property(property="name", type="string", example="Goku Atualizado"),
     *             @OA\Property(property="price", type="number", example=400.00),
     *             @OA\Property(property="condition", type="string", enum={"novo","usado","restaurado"}, example="usado"),
     *             @OA\Property(property="stock", type="integer", example=5),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */

    /**
     * @OA\Patch(
     *     path="/products/{id}",
     *     summary="Update product (partial)",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="stock", type="integer", example=10),
     *             @OA\Property(property="price", type="number", example=300.00)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(UpdateProductRequest $request, int $id)
    {
        if ($request->isMethod('patch') && empty($request->only([
            'name', 'description', 'price', 'rarity', 'condition', 'stock', 'category_id'
        ]))) {
            return response()->json(['message' => 'No fields provided'], 422);
        }

        try {
            return response()->json($this->service->update($id, $request->validated()));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete product",
     *     tags={"Products"},
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
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}