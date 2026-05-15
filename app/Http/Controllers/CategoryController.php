<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function show(int $id)
    {
        $category = Category::find($id);

        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->only(['name', 'description']));

        return response()->json($category, 201);
    }

    public function update(Request $request, int $id)
    {
        $category = Category::find($id);

        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        if ($request->isMethod('put')) {
            $request->validate([
                'name'        => 'required|string|max:100',
                'description' => 'nullable|string',
            ]);
        } else {
            $fields = $request->only(['name', 'description']);
            if (empty($fields)) {
                return response()->json(['message' => 'No fields provided'], 422);
            }
        }
        $category->update($request->only(['name', 'description']));

        return response()->json($category);
    }

    public function destroy(int $id)
    {
        $category = Category::find($id);

        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}