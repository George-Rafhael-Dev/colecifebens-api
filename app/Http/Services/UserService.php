<?php

namespace App\Http\Services;

use App\Models\User;

class UserService
{
    public function getAll(): mixed
    {
        return User::all();
    }

    public function getById(int $id): User
    {
        $user = User::find($id);
        if (!$user) throw new \Exception('User not found', 404);
        return $user;
    }

    public function create(array $data): User
    {
        return User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
            'cpf'        => $data['cpf'],
            'birth_date' => $data['birth_date'],
            'phone'      => $data['phone'],
        ]);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->getById($id);
        $user->update($data);
        return $user->fresh();
    }

    public function delete(int $id): array
    {
        $user = $this->getById($id);
        $user->delete();
        return ['message' => 'User deleted'];
    }
}