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

        if (!$product) return response()->json(['message' => 'Product not found'], 404);

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

    public function destroy(int $id)
    {
        $product = Product::find($id);

        if (!$product) return response()->json(['message' => 'Product not found'], 404);

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }
}