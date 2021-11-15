<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductAdsRequest extends FormRequest
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
            'name' => 'required|unique:product_ads|max:255',
            'text' => 'required',
            'textarea' => 'required',
            'image_path' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên slider không được để trống',
            'name.unique' => 'Tên slider không được trùng',
            'name.max' => 'Tên slider không dài quá 255 kí tự',
            'text.required' => 'Text slider không được để trống',
            'textarea.required' => 'Textarea slider không được để trống',
            'image.required' => 'Ảnh slider không được để trống',
        ];
    }
}
