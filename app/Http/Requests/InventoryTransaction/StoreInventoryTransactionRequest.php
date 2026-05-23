<?php

namespace App\Http\Requests\InventoryTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inventoryId' => ['required', 'integer', 'exists:inventory,inventory_id'],
            'transactionType' => ['required', Rule::in(['Import', 'Export'])],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'transactionDate' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'userId' => ['prohibited'],
        ];
    }
}
