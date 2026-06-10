<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0.01',
            'rarity'      => 'nullable|string|max:50',
            'condition'   => 'required|string|in:novo,usado,restaurado',
            'stock'       => 'required|integer|min:0',
            'user_id'     => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:categories,id',
        ];
    }
}