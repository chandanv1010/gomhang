<div class="panel-foot mt30 pay">
    <div class="cart-summary mb20">
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Giảm giá khuyến mại</span>
                <div class="summary-value discount-value">-{{ convert_price($cartPromotion['discount'] + $discountTotalProduct, true)  }}đ</div>
            </div>
        </div>
        @php
            // $voucherDiscount tinh boi CartVoucherService (CartService::checkoutData).
            $voucherDiscount = $voucherDiscount ?? 0;
            $shipCost = $shipping['totalShippingCost'] ?? 0;
        @endphp
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">
                    Giảm giá voucher
                    @if(!empty($appliedVoucher))
                        ({{ $appliedVoucher['voucher']->code }})
                    @endif
                </span>
                <div class="summary-value voucher-value">-{{ convert_price($voucherDiscount, true) }}đ</div>
            </div>
        </div>
        @if($shipCost > 0)
            <div class="cart-summary-item">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <span class="summay-title">Phí giao hàng</span>
                    <div class="summary-value ship-value">{{ convert_price($shipCost, true) }}đ</div>
                </div>
            </div>
        @endif

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
                    {{ (count($carts) && !is_null($carts) ) ? convert_price(max(0, $cartCaculate['cartTotal'] - $cartPromotion['discount'] - $voucherDiscount + $shipCost), true)  : 0   }}đ
                </div>
            </div>
        </div>
        <div class="buy-more">
            <a href="{{ write_url('khoa-hoc') }}" class="btn-buymore">Chọn thêm sản phẩm khác</a>
        </div>
    </div>
</div>