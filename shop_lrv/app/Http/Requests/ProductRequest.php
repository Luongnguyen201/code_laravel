<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|unique:products|max:255|min:5',
            'price' => 'required',
            'parent_id' => 'required',
            'content' => 'required',
            'content_detail' => 'required',
            'feature_image_path' => 'required',
            'image_path' => 'required',
            'tags' => 'required',
            'quantity' => 'required|max:9999|min:1',
            'import_price' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Điền tên sản phẩm',
            'name.unique' => 'Tên sản phẩm không được trùng',
            'name.max' => 'Tên sản phẩm không được phép dài quá 255 kí tự',
            'name.min' => 'Tên sản phẩm phải nhiều hơn 5 kí tự',
            'price.required' => 'Điền giá xuất sản phẩm',
            'parent_id.required' => 'Chọn danh mục',
            'content.required' => 'Điền nội dung chi tiết sản phẩm',
            'feature_image_path.required' => 'Chọn ảnh đại diện',
            'image_path.required' => 'Chọn ảnh chi tiết',
            'tags.required' => 'Tag không để trống',
            'content_detail.required' => 'Chi tiết sản phẩm không được để trống',
            'quantity.required' => 'Số lượng sản phẩm không được để trống',
            'quantity.min' => 'Số lượng sản phẩm không dưới 1',
            'quantity.max' => 'Số lượng sản phẩm không hơn 9999',
            'import_price.required' => 'Điền giá nhập sản phẩm',
        ];
    }
}
