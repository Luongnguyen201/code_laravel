<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderAddRequest extends FormRequest
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
            'name' => 'required|unique:sliders|max:255|',
            'description' => 'required',
            'image_path' => 'required',
        ];
    }
    public function messages(){
        return [
            'name.required' => 'Tên Slider không được để trống',
            'name.unique' => 'Tên Slider không được trùng',
            'name.max' => 'Tên Slider không được phép dài quá 255 kí tự',
            'description' => 'Điền mô tả Slider',
            'image_path.required' => 'Chọn Ảnh Slider',
        ];
    }
}
