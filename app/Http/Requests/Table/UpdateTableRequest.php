<?php

namespace App\Http\Requests\Table;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:50', Rule::unique('tables', 'name')->ignore($id, 'table_id')],
            'status' => ['sometimes', Rule::in(['Available', 'Occupied'])],
        ];
    }
}
