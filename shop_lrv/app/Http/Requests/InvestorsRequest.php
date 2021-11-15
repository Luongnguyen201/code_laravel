<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvestorsRequest extends FormRequest
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
            'image_path' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên logo không được để trống',
            'name.unique' => 'Tên logo không được trùng',
            'name.max' => 'Tên logo không dài quá 255 kí tự',
            'image_path.required' => 'Ảnh logo không được để trống',
        ];
    }
}
