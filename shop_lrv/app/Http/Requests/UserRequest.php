<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|max:255|min:6',
            'role_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Họ Tên Không Được Để Trống.',
            'name.max' => 'Họ Tên Không Dài Quá 255 Kí Tự.',
            'email.required' => 'Email Không Được Để Trống.',
            'email.unique' => 'Email Đã Tồn Tại.',
            'email.max' => 'Email Không Được Dài Quá 255 Kí Tự.',
            'password.required' => 'Password Không Được Để Trống.',
            'password.min' => 'Password Phải Dài Hơn 6 Kí Tự.',
            'role_id.required' => 'Vai Trò Không Được Để Trống.', 
        ];
    }
}
