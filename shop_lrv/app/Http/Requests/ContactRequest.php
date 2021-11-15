<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            'name' =>  'required|unique:manuses|max:255',
            'content' => 'required',
            'id_menu' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên tiêu đề không được để trống',
            'name.unique' => 'Tên tiêu đề không được trùng',
            'name.max' => 'Tên tiêu đề phải ngắn hơn 255 kí tự',
            'content.required' => 'Nội dung không được để trống',
            'id_menu.required' => 'Menu không được để trống', 
        ];
    }
}
