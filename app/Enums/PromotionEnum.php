<?php 
namespace App\Enums;

enum PromotionEnum: string {
    const ORDER_AMOUNT_RANGE = 'order_amount_range';
    const PRODUCT_AND_QUANTITY = 'product_and_quantity';
    const BUY_PRODUCT_TAKE_GIFT = 'buy_product_take_gift';
    const BUY_COMBO = 'buy_combo';
    const MODULE_TYPE = 'module_type';
    const DISCOUNT = 'discountInformation';
    const PROMOTION = 'PROMOTION';
    const SINGLE_PRODUCT = 'single_product';
    const MULTIPLE_PRODUCT = 'multiple_product';
}