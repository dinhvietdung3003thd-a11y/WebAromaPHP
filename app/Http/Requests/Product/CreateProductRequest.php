<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'categoryId' => ['required', 'integer', 'exists:categories,category_id'],
            'imageUrl' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'isAvailable' => ['required', 'boolean'],
        ];
    }
}
