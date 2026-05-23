<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orderDate' => ['nullable', 'date'],
            'tableId' => ['nullable', 'integer', 'exists:tables,table_id'],
            'note' => ['nullable', 'string'],
            'details' => ['required', 'array', 'min:1'],
            'details.*.productId' => ['required', 'integer', 'distinct', 'exists:products,product_id'],
            'details.*.quantity' => ['required', 'integer', 'min:1'],
            'totalAmount' => ['prohibited'],
            'unitPrice' => ['prohibited'],
            'subtotal' => ['prohibited'],
        ];
    }
}
