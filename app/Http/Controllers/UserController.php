<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="List all users",
     *     tags={"Users"},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index()
    {
        return response()->json($this->service->getAll());
    }
    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Show user",
     *     tags={"Users"},
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
     *     path="/users",
     *     summary="Create user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","cpf","birth_date","phone"},
     *             @OA\Property(property="name", type="string", example="George"),
     *             @OA\Property(property="email", type="string", example="george@email.com"),
     *             @OA\Property(property="password", type="string", example="123456"),
     *             @OA\Property(property="cpf", type="string", example="000.000.000-00"),
     *             @OA\Property(property="birth_date", type="string", example="2000-01-01"),
     *             @OA\Property(property="phone", type="string", example="81999999999")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreUserRequest $request)
    {
        return response()->json($this->service->create($request->validated()), 201);
    }
    /**
     * @OA\Patch(
     *     path="/users/{id}",
     *     summary="Update user (partial)",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="George"),
     *             @OA\Property(property="phone", type="string", example="81777777777")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        if ($request->isMethod('patch') && empty($request->only(['name', 'email', 'phone', 'birth_date']))) {
            return response()->json(['message' => 'No fields provided'], 422);
        }

        try {
            return response()->json($this->service->update($id, $request->validated()));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }
    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete user",
     *     tags={"Users"},
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