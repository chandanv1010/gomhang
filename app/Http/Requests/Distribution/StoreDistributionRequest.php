<?php

namespace App\Http\Requests\Distribution;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'phone' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'province_id' => 'required|integer|gt:0',
            'district_id' => 'required|integer|gt:0',
            'email' => 'nullable|email|max:191',
            'map' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên nhà phân phối.',
            'name.string' => 'Tên phải là dạng ký tự.',
            'name.max' => 'Tên tối đa 191 ký tự.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.max' => 'Số điện thoại tối đa 50 ký tự.',
            'address.required' => 'Bạn chưa nhập địa chỉ.',
            'address.max' => 'Địa chỉ tối đa 255 ký tự.',
            'province_id.required' => 'Bạn chưa chọn Miền (phía Bắc / phía Nam).',
            'province_id.gt' => 'Bạn chưa chọn Miền (phía Bắc / phía Nam).',
            'district_id.required' => 'Bạn chưa chọn Tỉnh / Thành phố.',
            'district_id.gt' => 'Bạn chưa chọn Tỉnh / Thành phố.',
            'email.email' => 'Email chưa đúng định dạng.',
            'email.max' => 'Email tối đa 191 ký tự.',
            'map.required' => 'Bạn chưa nhập mã nhúng bản đồ Google Map.',
        ];
    }
}
