<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productId' => ['sometimes', 'integer', 'exists:products,product_id'],
            'inventoryId' => ['sometimes', 'integer', 'exists:inventory,inventory_id'],
            'quantityNeeded' => ['sometimes', 'numeric', 'gt:0'],
        ];
    }
}
