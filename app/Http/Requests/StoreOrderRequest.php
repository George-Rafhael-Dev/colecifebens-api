<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'user_id'             => 'required|integer|exists:users,id',
            'payment_method'      => 'required|string|in:pix,cartao_credito,boleto',
            'products'            => 'required|array|min:1',
            'products.*.id'       => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
}