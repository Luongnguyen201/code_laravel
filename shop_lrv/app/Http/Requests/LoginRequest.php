<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|max:255',
            'password' => 'required|max:6|min:6',
            'name' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Email không được để trống !',
            'email.max' => 'Email không dài quá 255 kí tự !',
            'password.required' => 'Password không được để trống !',
            'password.max' => 'Password không dài quá 6 kí tự',
            'password.min' => 'Password không ngắn quá 6 kí tự',
            'name.required' => 'Họ và tên không được để trống !',
        ];
    }
}
