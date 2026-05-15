<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function show(int $id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:150|unique:users,email',
            'password'   => 'required|string|min:6',
            'cpf'        => 'required|string|size:14|unique:users,cpf',
            'birth_date' => 'required|date',
            'phone'      => 'required|string|max:20',
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

    public function update(Request $request, int $id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        if ($request->isMethod('put')) {
            $request->validate([
                'name'       => 'required|string|max:100',
                'email'      => 'required|email|max:150|unique:users,email,' . $id,
                'phone'      => 'required|string|max:20',
                'birth_date' => 'required|date',
            ]);
        } else {
            $fields = $request->only(['name', 'email', 'phone', 'birth_date']);
            
            if (empty($fields)) {
                return response()->json(['message' => 'No fields provided'], 422);
            }

            $request->validate([
                'email' => 'sometimes|email|unique:users,email,' . $id,
            ]);
        }

        $user->update($request->only(['name', 'email', 'phone', 'birth_date']));

        return response()->json($user);
    }

    public function destroy(int $id)
    {
        $user = User::find($id);

        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}