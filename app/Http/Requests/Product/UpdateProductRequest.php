<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            'name' => 'required',
            // 'total_lesson' => 'required',
            // 'duration' => 'required',
            // 'lecturer_id' => 'gt:0',
            'canonical' => 'required|unique:routers,canonical, ' . $this->id . ',module_id',
            'product_catalogue_id' => 'gt:0',
            'stock' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào ô tiêu đề.',
            // 'total_lesson.required' => 'Bạn chưa nhập vào số lượng.',
            // 'duration.required' => 'Bạn chưa nhập vào thời lượng.',
            // 'lecturer_id.gt' => 'Bạn chưa chọn giảng viên.',
            'canonical.required' => 'Bạn chưa nhập vào ô đường dẫn',
            'canonical.unique' => 'Đường dẫn đã tồn tại, Hãy chọn đường dẫn khác',
            'product_catalogue_id.gt' => 'Bạn phải nhập vào danh mục cha',
            'stock.integer' => 'Tồn kho phải là số nguyên.',
            'stock.min' => 'Tồn kho không được âm.',
        ];
    }
}
