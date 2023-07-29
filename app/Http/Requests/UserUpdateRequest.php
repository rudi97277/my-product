<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
        $user = auth()->user();
        return [
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => [
                'string',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'string',
            'date_of_birth' => 'date',
            'old_password' => 'string',
            'password' => 'required_with:old_password|string|min:6|confirmed',
        ];
    }
}
