<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;
        return [
            'first_name' => $this->isMethod('post') ? 'required|string|max:255' : 'string|max:255',
            'last_name' => $this->isMethod('post') ? 'required|string|max:255' : 'string|max:255',
            'email' => $this->isMethod('post')
                ? 'required|email|max:255|unique:users,email'
                : 'email|max:255|unique:users,email,' . $userId,
            'address.country' => 'nullable|string|max:255',
            'address.city' => 'nullable|string|max:255',
            'address.post_code' => 'nullable|string|max:255',
            'address.street' => 'nullable|string|max:255',
        ];
    }
}