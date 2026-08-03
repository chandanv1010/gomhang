<?php

namespace App\Http\Requests\DistributionArea;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributionAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'parent_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên khu vực.',
            'name.string' => 'Tên khu vực phải là dạng ký tự.',
            'name.max' => 'Tên khu vực tối đa 191 ký tự.',
            'parent_id.required' => 'Bạn chưa chọn danh mục cha.',
            'parent_id.integer' => 'Danh mục cha phải là dạng số nguyên.',
        ];
    }
}
