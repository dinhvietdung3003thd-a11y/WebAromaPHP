<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:100', Rule::unique('inventory', 'name')->ignore($id, 'inventory_id')],
            'unit' => ['sometimes', 'string', 'max:20'],
            'quantityInStock' => ['sometimes', 'numeric', 'min:0'],
            'minThreshold' => ['sometimes', 'numeric', 'min:0'],
            'supplierId' => ['sometimes', 'nullable', 'integer', 'exists:suppliers,supplier_id'],
        ];
    }
}
