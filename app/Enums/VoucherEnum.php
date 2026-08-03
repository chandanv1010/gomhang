<?php 
namespace App\Enums;

enum VoucherEnum: string {
    const VOUCHER_TOTAL_ORDER = 'voucher_total_order';
    const VOUCHER_SHIP = 'ship_voucher';
    const PRODUCT_VOUCHER = 'PRODUCT_VOUCHER';
    const ALL_PRODUCTS = 'ALL_PRODUCTS';
    const SPECIFIC_PRODUCTS = 'SPECIFIC_PRODUCTS';
    const SHIPPING_ORDERS = 'SHIPPING_ORDERS';
    const TOTAL_ORDERS = 'TOTAL_ORDERS';
    const COMBINE_PROMOTION = 1;
    const NO_COMBINE_PROMOTION = 0;
    const FIXED = 'FIXED';
    const PERCENTAGE = 'PERCENTAGE';
    const QUANTITY = 1;
}

