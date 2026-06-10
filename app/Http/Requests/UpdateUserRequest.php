<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user');

        if ($this->isMethod('put')) {
            return [
                'name'       => 'required|string|max:100',
                'email'      => 'required|email|max:150|unique:users,email,' . $id,
                'phone'      => ['required', 'string', 'regex:/^\d{10,11}$/'],
                'birth_date' => 'required|date',
            ];
        }

        return [
            'name'       => 'sometimes|string|max:100',
            'email'      => 'sometimes|email|max:150|unique:users,email,' . $id,
            'phone'      => ['sometimes', 'string', 'regex:/^\d{10,11}$/'],
            'birth_date' => 'sometimes|date',
        ];
    }
}