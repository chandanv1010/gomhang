<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email|string|unique:customers,email|max:191',
            'phone' => [
                'required',
                'regex:/^(0[3|5|7|8|9])+([0-9]{8})$/', // chuẩn Viettel, Mobi, Vina
            ],
            'address' => 'required|string|min:5|max:200',
            'password' => 'required|string|min:6|max:50',
            're_password' => 'required|string|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'Bạn chưa nhập họ tên.',
            'name.min' => 'Họ tên phải có ít nhất 2 ký tự.',
            'name.max' => 'Họ tên không vượt quá 50 ký tự.',

            // Email
            'email.required' => 'Bạn chưa nhập email.',
            'email.email' => 'Email chưa đúng định dạng (vd: abc@gmail.com).',
            'email.unique' => 'Email đã tồn tại. Hãy chọn email khác.',
            'email.max' => 'Email tối đa 191 ký tự.',

            // Phone
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',

            // Address
            'address.required' => 'Bạn chưa nhập địa chỉ.',
            'address.min' => 'Địa chỉ phải có ít nhất 5 ký tự.',
            'address.max' => 'Địa chỉ không vượt quá 200 ký tự.',

            // Password
            'password.required' => 'Bạn chưa nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
            'password.max' => 'Mật khẩu không vượt quá 50 ký tự.',

            // Confirm Password
            're_password.required' => 'Bạn chưa nhập lại mật khẩu.',
            're_password.same' => 'Mật khẩu không khớp.',
        ];
    }
}
