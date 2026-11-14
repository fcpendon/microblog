<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsDetailsRequest extends FormRequest
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
            'name' => 'min:4|max:100',
            'username' => 'min:4|max:20|unique:users,username,'.auth()->id(),
            'email' => 'required|email|max:100|unique:users,email,'.auth()->id(),
        ];
    }
}
