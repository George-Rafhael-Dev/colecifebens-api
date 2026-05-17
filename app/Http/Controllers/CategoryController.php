<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="List all categories",
     *     tags={"Categories"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return response()->json(Category::all());
    }
    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Show category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id)
    {
        $category = Category::find($id);

        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        return response()->json($category);
    }
    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Create category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Action Figures"),
     *             @OA\Property(property="description", type="string", example="Bonecos colecionáveis")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($request->only(['name', 'description']));

        return response()->json($category, 201);
    }
    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Update category (all fields)",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Cartas"),
     *             @OA\Property(property="description", type="string", example="Cards colecionáveis")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */

    /**
     * @OA\Patch(
     *     path="/categories/{id}",
     *     summary="Update category (partial)",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", example="Nova descrição")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
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
    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy(int $id)
    {
        $category = Category::find($id);

        if (!$category) return response()->json(['message' => 'Category not found'], 404);

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}