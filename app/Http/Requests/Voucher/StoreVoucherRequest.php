<?php

namespace App\Http\Requests\Voucher;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
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
            'code' => 'unique:vouchers',
            'total_quantity' => 'required',
            'max_usage_per_user' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập tên Voucher',
            'code.unique' => 'Mã Voucher đã tồn tại',
            'total_quantity.required' => 'Bạn chưa nhập số lượng',
            'max_usage_per_user.required' => 'Bạn chưa nhập số lần sử dụng / User',
            'start_date.required' => 'Bạn chưa nhập ngày bắt đầu',
            'end_date.required' => 'Bạn chưa nhập ngày kết thúc',
        ];
    }
}

