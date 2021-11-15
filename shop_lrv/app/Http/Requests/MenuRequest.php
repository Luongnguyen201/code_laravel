<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'name' => 'required|unique:investors|max:255',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên menu không được để trống',
            'name.unique' => 'Tên menu không được trùng',
            'name.max' => 'Tên menu không dài quá 255 kí tự',
        ];
    }
}
