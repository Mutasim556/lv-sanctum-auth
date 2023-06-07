<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:6|max:255',
            'user_name' => 'required|min:6|max:255|unique:users,user_name',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:255',
            'password_again' => 'required|min:6|max:255|same:password'
        ];
    }
}
