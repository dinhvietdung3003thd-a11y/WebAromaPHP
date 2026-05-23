<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:inventory,name'],
            'unit' => ['required', 'string', 'max:20'],
            'quantityInStock' => ['nullable', 'numeric', 'min:0'],
            'minThreshold' => ['nullable', 'numeric', 'min:0'],
            'supplierId' => ['nullable', 'integer', 'exists:suppliers,supplier_id'],
        ];
    }
}
