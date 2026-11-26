<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
            'fullname' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'province_id' => 'required|exists:provinces,code',
            // Phuong/xa phai thuoc dung tinh da chon (dia gioi sau sap nhap, 2 cap).
            'ward_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('wards', 'code')->where('province_code', (string) $this->input('province_id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Bạn chưa nhập Họ Tên.',
            'phone.required' => 'Bạn chưa nhập Số điện thoại.',
            'address.required' => 'Bạn chưa nhập địa chỉ.',
            'province_id.required' => 'Bạn chưa chọn Tỉnh/Thành phố.',
            'province_id.exists' => 'Tỉnh/Thành phố không hợp lệ.',
            'ward_id.required' => 'Bạn chưa chọn Phường/Xã.',
            'ward_id.exists' => 'Phường/Xã không thuộc Tỉnh/Thành phố đã chọn.',
        ];
    }
}
