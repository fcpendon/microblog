<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:4|max:100',
            'username' => 'required|min:4|max:20|unique:users,username',
            'password' => 'required|min:8|max:16|confirmed',
            'email' => 'required|email|max:100|unique:users,email',
        ];
    }
}
