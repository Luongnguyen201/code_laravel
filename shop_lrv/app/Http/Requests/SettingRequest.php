<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'config_key'=> 'required|unique:settings|max:255',
            'config_value' => 'required',
            'image_path' =>'required',
        ];
    }
    public function messages()
{
    return [
        'config_key.required' => 'Config Key không được để trống',
        'config_value.required' => 'Config Value không được để trống',
        'config_key.unique' =>'Config Key không được trùng',
        'config_key.max' => 'Config Key không dài quá 255 kí tự',
        'image_path.required' => 'Ảnh đại diện không được trống',
    ];
}
}
