<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
        return response()->json(User::all());
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
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        return response()->json($user);
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
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:150|unique:users,email',
            'password'   => 'required|string|min:6',
            'cpf' => [
                'required',
                'string',
                'size:14',
                'unique:users,cpf',
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/'
            ],
            'birth_date' => 'required|date',
            'phone' => ['required','string','regex:/^\d{10,11}$/'],        
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'cpf'        => $request->cpf,
            'birth_date' => $request->birth_date,
            'phone'      => $request->phone,
        ]);

        return response()->json($user, 201);
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
    public function update(Request $request, int $id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        if ($request->isMethod('put')) {
            $request->validate([
                'name'       => 'required|string|max:100',
                'email'      => 'required|email|max:150|unique:users,email,' . $id,
                'phone'      => ['required', 'string', 'regex:/^\d{10,11}$/'],
                'birth_date' => 'required|date',
            ]);
        } else {
            $fields = $request->only(['name', 'email', 'phone', 'birth_date']);
            
            if (empty($fields)) {
                return response()->json(['message' => 'No fields provided'], 422);
            }

            $request->validate([
                'name'       => 'sometimes|string|max:100',
                'email'      => 'sometimes|email|max:150|unique:users,email,' . $id,
                'phone'      => ['sometimes', 'string', 'regex:/^\d{10,11}$/'],
                'birth_date' => 'sometimes|date',
            ]);
        }

        $user->update($request->only(['name', 'email', 'phone', 'birth_date']));

        return response()->json($user);
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
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}