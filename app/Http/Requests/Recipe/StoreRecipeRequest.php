<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productId' => ['required', 'integer', 'exists:products,product_id'],
            'inventoryId' => ['required', 'integer', 'exists:inventory,inventory_id'],
            'quantityNeeded' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
