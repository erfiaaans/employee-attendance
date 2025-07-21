<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|string|max:255|unique:users,user_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:' . implode(',', \App\Enums\UserRole::values()),
            'profile_picture_url' => 'nullable|url|max:2048',
            'password' => 'required|string|min:8',
            'gender' => 'nullable|in:' . implode(',', \App\Enums\UserGender::values()),
            'telephone' => 'nullable|string|max:15',
            'city_work' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:100',
        ];
    }
}
