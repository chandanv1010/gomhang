<?php

namespace App\Rules\Promotion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

use function PHPUnit\Framework\isEmpty;

class BuyProductTakeGiftRule implements ValidationRule
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

        if(!isset($this->data['products']) && !isset($this->data['product_gifts'])){
            $fail('Bạn chưa chọn sản phẩm mua và quà tặng');
        }
        
        if(!isset($this->data['products']) && isset($this->data['product_gifts'])){
            $fail('Bạn chưa chọn sản phẩm mua');
        }

        if(!isset($this->data['product_gifts']) && isset($this->data['products'])){
            $fail('Bạn chưa chọn quà tặng');
        }

        if(isset($this->data['products']) && $this->hasAnyNull($this->data['products']['quantity'])){
            $fail('Bạn phải nhập số lượng cho từng sản phẩm.');
        }

        if(isset($this->data['products']) && $this->inValidQuantity($this->data['products']['quantity'])){
            $fail('Số lượng sản phẩm tối thiểu là 1.');
        }

        if(isset($this->data['product_gifts']) && $this->hasAnyNull($this->data['product_gifts']['quantity'])){
            $fail('Bạn phải nhập số lượng cho từng quà tặng.');
        }

        if(isset($this->data['product_gifts']) && $this->inValidQuantity($this->data['product_gifts']['quantity'])){
            $fail('Số lượng quà tặng tối thiểu là 1.');
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
