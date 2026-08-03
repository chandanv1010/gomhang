<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array|min:1',
            'name.*' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'name')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên dự án',
            'name.array' => 'Dữ liệu không hợp lệ',
            'name.min' => 'Phải có ít nhất một dự án',
            'name.*.required' => 'Tên dự án không được để trống',
            'name.*.string' => 'Tên dự án phải là dạng ký tự',
            'name.*.max' => 'Tên dự án không được vượt quá 255 ký tự',
            'name.*.unique' => 'Tên dự án ":input" đã tồn tại trong hệ thống',
        ];
    }
}