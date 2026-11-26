<?php

namespace App\Rules\Promotion;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductAndQuantityRule implements ValidationRule
{

    protected $data;

    public function __construct($data){
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        if($this->data['product_and_quantity']['quantity'] == 0){
            $fail('Bạn phải nhập số lượng mua tối thiểu để hưởng chiết khấu');
        }

        if($this->data['product_and_quantity']['discountValue'] == 0){
            $fail('Bạn phải nhập vào giá trị của chiết khấu');
        }

        // "Giam toi da" tinh bang dong. Da co nguoi go 50 (tuong la 50%) nen
        // op 40.000d chi duoc giam 50d - nhin ngoai trang nhu khong co khuyen mai.
        $max = (float) str_replace(['.', ','], '', (string) ($this->data['product_and_quantity']['maxDiscountValue'] ?? 0));
        if($max > 0 && $max < 1000){
            $fail('Giảm tối đa tính bằng đồng (ví dụ 50.000), không phải %. Để 0 nếu không giới hạn.');
        }

        if(!isset($this->data['object'])){
            $fail('Bạn chưa chọn đối tượng áp dụng chiết khấu');
        }
    }
}
