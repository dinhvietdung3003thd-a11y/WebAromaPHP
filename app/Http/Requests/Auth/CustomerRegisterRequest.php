<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:100', 'unique:customers,username'],
            'password' => ['required', 'string', 'min:6'],
            'fullName' => ['required', 'string', 'max:150'],
            'phoneNumber' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', 'unique:customers,email'],
        ];
    }
}
