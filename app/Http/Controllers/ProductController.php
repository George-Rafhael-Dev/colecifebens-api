<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index()
    {
        return response()->json(Product::all());
    }
    
    public function show(int $id)
    {
        $product = Product::find($id);

        if (!$product) return response()->json(['memssagem' => 'Produto não encontrado'], 404);

        return response()->json($product);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0.01',
            'rarity'      => 'nullable|string|max:50',
            'condition'   => 'required|string|in:novo,usado,restaurado',
            'stock'       => 'required|integer|min:0',
            'user_id'     => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $product = Product::create($request->only([
            'name', 'description', 'price', 'rarity',
            'condition', 'stock', 'user_id', 'category_id',
        ]));

        return response()->json($product, 201);
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
    public function update(Request $request, int $id)
    {
        $product = Product::find($id);

        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        if ($request->isMethod('put')) {
            $request->validate([
                'name'        => 'required|string|max:150',
                'price'       => 'required|numeric|min:0.01',
                'condition'   => 'required|string|in:novo,usado,restaurado',
                'stock'       => 'required|integer|min:0',
                'category_id' => 'required|integer|exists:categories,id',
            ]);
        } else {
            $fields = $request->only(['name', 'description', 'price', 'rarity', 'condition', 'stock', 'category_id']);
            if (empty($fields)) {
                return response()->json(['message' => 'No fields provided'], 422);
            }

            $request->validate([
                'price'       => 'sometimes|numeric|min:0.01',
                'condition'   => 'sometimes|string|in:novo,usado,restaurado',
                'stock'       => 'sometimes|integer|min:0',
                'category_id' => 'sometimes|integer|exists:categories,id',
            ]);
        }

        $product->update($request->only([
            'name', 'description', 'price', 'rarity',
            'condition', 'stock', 'category_id',
        ]));

        return response()->json($product);
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
        $product = Product::find($id);

        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}