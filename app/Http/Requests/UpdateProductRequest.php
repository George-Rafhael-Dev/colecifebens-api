<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        if ($this->isMethod('put')) {
            return [
                'name'        => 'required|string|max:150',
                'price'       => 'required|numeric|min:0.01',
                'condition'   => 'required|string|in:novo,usado,restaurado',
                'stock'       => 'required|integer|min:0',
                'category_id' => 'required|integer|exists:categories,id',
            ];
        }

        return [
            'name'        => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0.01',
            'rarity'      => 'sometimes|string|max:50',
            'condition'   => 'sometimes|string|in:novo,usado,restaurado',
            'stock'       => 'sometimes|integer|min:0',
            'category_id' => 'sometimes|integer|exists:categories,id',
        ];
    }
}