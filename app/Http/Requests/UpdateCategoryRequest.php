<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        if ($this->isMethod('put')) {
            return [
                'name'        => 'required|string|max:100',
                'description' => 'nullable|string',
            ];
        }

        return [
            'name'        => 'sometimes|string|max:100',
            'description' => 'nullable|string',
        ];
    }
}