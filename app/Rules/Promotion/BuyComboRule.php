<?php

namespace App\Rules\Promotion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function PHPUnit\Framework\isEmpty;

class BuyComboRule implements ValidationRule
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

        if(!isset($this->data['product_combos']['id'])){
            $fail('Bạn chưa chọn sản phẩm mua');
            return;
        }
        
        if(isset($this->data['product_combos']) && $this->hasAnyNull($this->data['product_combos']['quantity'])){
            $fail('Bạn phải nhập số lượng cho từng sản phẩm.');
        }

        if(isset($this->data['product_combos']) && $this->inValidQuantity($this->data['product_combos']['quantity'])){
            $fail('Số lượng sản phẩm tối thiểu là 1.');
        }

        if(!isset($this->data['product_combos']['price'])){
            $fail('Bạn chưa nhập tổng tiền');
        }


    }

    private function hasAnyNull(array $quantities): bool{
        foreach ($quantities as $quantity) {
            if ($quantity === "null") {
                return true; 
            }
        }
        return false;
    }

    private function inValidQuantity(array $quantities): bool{
        foreach ($quantities as $quantity) {
            if($quantity == 0) {
                return true; 
            }
        }
        return false;
    }
}
