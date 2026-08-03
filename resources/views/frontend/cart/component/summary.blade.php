<div class="panel-foot mt30 pay">
    <div class="cart-summary mb20">
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Giảm giá khuyến mại</span>
                <div class="summary-value discount-value">-{{ convert_price($cartPromotion['discount'] + $discountTotalProduct, true)  }}đ</div>
            </div>
        </div>
        @php
            $voucherTotal = (!is_null($voucher) && $voucher['product_scope'] == 'TOTAL_ORDERS') ? $voucher['discount'] : 0;
            $voucherShip = (!is_null($voucher) && $voucher['product_scope'] == 'SHIPPING_ORDERS') ? $voucher['discount'] : 0;
        @endphp
        {{-- <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Giảm giá voucher</span>
                <div class="summary-value voucher-value">
                    -{{ convert_price($totalVoucherProduct + $voucherTotal + $voucherShip ?? 0, true) }}đ
                </div>
            </div>
        </div> --}}
        {{-- <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Phí giao hàng</span>
                <div class="summary-value ship-value">{{ convert_price(($shipping['totalShippingCost'] ?? 0) - $voucherShip ?? 0, true) }}đ</div>
            </div>
        </div> --}}

        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Giảm giá từ điểm</span>
                <div class="summary-value discount-point">
                    -0đ
                </div>
            </div>
            
        </div>
        
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title bold">Tổng tiền</span>
                <div class="summary-value cart-total">
                    {{ (count($carts) && !is_null($carts) ) ? convert_price($cartCaculate['cartTotal'] - $cartPromotion['discount'] - $totalVoucherProduct - ($voucherTotal  ?? 0 ) + (($shipping['totalShippingCost'] ?? 0) - $voucherShip), true)  : 0   }}đ
                </div>
            </div>
        </div>
        <div class="buy-more">
            <a href="{{ write_url('khoa-hoc') }}" class="btn-buymore">Chọn thêm sản phẩm khác</a>
        </div>
    </div>
</div>