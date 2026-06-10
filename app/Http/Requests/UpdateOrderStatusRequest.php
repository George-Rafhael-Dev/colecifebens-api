<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'status'         => 'sometimes|string|in:pendente,enviado,entregue,cancelado',
            'payment_status' => 'sometimes|string|in:aguardando,aprovado,recusado',
            'payment_method' => 'sometimes|string|in:pix,cartao_credito,boleto',
        ];
    }
}