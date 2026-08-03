<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = Auth::guard('customer')->id();

        return [
            'name'      => 'required|string|min:2|max:50',
            'email'     => 'required|string|email|max:191|unique:customers,email,' . $id,
            'phone' => [
                'nullable',
                'regex:/^(0[3|5|7|8|9])[0-9]{8}$/'
            ],
            'address'   => 'required|string|min:5|max:200',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập họ tên.',
            'name.min'      => 'Họ tên phải ít nhất 2 ký tự.',
            'name.max'      => 'Họ tên tối đa 50 ký tự.',

            'email.required' => 'Bạn chưa nhập email.',
            'email.email'    => 'Email chưa đúng định dạng.',
            'email.unique'   => 'Email đã được sử dụng.',
            'email.max'      => 'Email tối đa 191 ký tự.',

            'phone.regex' => 'Số điện thoại không đúng định dạng.',

            'address.required' => 'Bạn chưa nhập địa chỉ.',
            'address.min'      => 'Địa chỉ ít nhất 5 ký tự.',
            'address.max'      => 'Địa chỉ tối đa 200 ký tự.',
        ];
    }
}
