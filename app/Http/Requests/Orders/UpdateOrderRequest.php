<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orderDate' => ['nullable', 'date'],
            'tableId' => ['sometimes', 'nullable', 'integer', 'exists:tables,table_id'],
            'note' => ['sometimes', 'nullable', 'string'],
            'details' => ['sometimes', 'array', 'min:1'],
            'details.*.productId' => ['required_with:details', 'integer', 'distinct', 'exists:products,product_id'],
            'details.*.quantity' => ['required_with:details', 'integer', 'min:1'],
            'totalAmount' => ['prohibited'],
            'unitPrice' => ['prohibited'],
            'subtotal' => ['prohibited'],
        ];
    }
}
