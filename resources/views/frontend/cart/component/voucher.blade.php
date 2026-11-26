{{--
    Khoi voucher o gio hang. Duoc render lai qua ajax (ajax/cart/applyCartVoucher,
    ajax/cart/unUseVoucher) nen chi dung cac bien tu CartService::checkoutData().
--}}
<div class="panel-voucher" id="cart-voucher-box">
    <div class="voucher-head">Mã giảm giá</div>

    @if(is_null($buyer))
        <p class="voucher-note">
            <a href="{{ route('customer.login') }}">Đăng nhập</a> để sử dụng mã giảm giá.
        </p>
    @else
        @if(!empty($voucherDropReason))
            <div class="voucher-alert">Mã giảm giá đã được gỡ: {{ $voucherDropReason }}</div>
        @endif

        @if(!empty($appliedVoucher))
            <div class="voucher-applied">
                <div>
                    <strong>{{ $appliedVoucher['voucher']->code }}</strong>
                    <span>- giảm {{ convert_price($appliedVoucher['discount'], true) }}đ</span>
                </div>
                <button type="button" class="voucher-remove" data-id="{{ $appliedVoucher['voucher']->id }}">Bỏ mã</button>
            </div>
        @else
            <div class="voucher-form uk-flex uk-flex-middle">
                <input type="text" class="input-text voucher-code-input" placeholder="Nhập mã giảm giá" autocomplete="off">
                <button type="button" class="voucher-code-apply">Áp dụng</button>
            </div>
        @endif

        @if(isset($allVoucherTotal) && count($allVoucherTotal))
            <div class="voucher-list">
                @foreach($allVoucherTotal as $v)
                    @php
                        $isApplied = !empty($appliedVoucher) && $appliedVoucher['voucher']->id == $v->id;
                        $isPercent = $v->discount_type == App\Enums\VoucherEnum::PERCENTAGE;
                        $remain = max(0, (int) $v->total_quantity - (int) $v->used_quantity);
                    @endphp
                    <div
                        class="voucher-item {{ $isApplied ? 'use' : '' }} {{ $v->preview_error ? 'voucher-disabled' : 'coupon-use' }}"
                        data-id="{{ $v->id }}"
                        title="{{ $v->preview_error }}"
                    >
                        <div class="voucher-left"></div>
                        <div class="voucher-right">
                            <div class="voucher-title">{{ $v->code }} <span>(Còn {{ $remain }})</span></div>
                            <div class="voucher-description">
                                <p>
                                    Giảm {{ $isPercent ? rtrim(rtrim(number_format($v->discount_value, 2, ',', '.'), '0'), ',') . '%' : convert_price($v->discount_value, true) . 'đ' }}
                                    @switch($v->product_scope)
                                        @case(App\Enums\VoucherEnum::TOTAL_ORDERS)
                                            cho đơn từ {{ convert_price($v->voucher_order_conditions->min_order_value ?? 0, true) }}đ
                                            @break
                                        @case(App\Enums\VoucherEnum::SHIPPING_ORDERS)
                                            phí giao hàng
                                            @break
                                        @case(App\Enums\VoucherEnum::SPECIFIC_PRODUCTS)
                                            cho sản phẩm áp dụng
                                            @break
                                        @default
                                            mỗi sản phẩm
                                    @endswitch
                                    @if((int) $v->max_discount_amount > 0)
                                        , tối đa {{ convert_price($v->max_discount_amount, true) }}đ
                                    @endif
                                </p>
                                <p class="voucher-expire">HSD: {{ \Carbon\Carbon::parse($v->end_date)->format('d/m/Y H:i') }}</p>
                                @if($v->preview_error)
                                    <p class="voucher-reason">{{ $v->preview_error }}</p>
                                @elseif(!$isApplied)
                                    <p class="voucher-save">Bấm để áp dụng: giảm {{ convert_price($v->preview_discount, true) }}đ</p>
                                @else
                                    <p class="voucher-save">Đang áp dụng - bấm để bỏ</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
<style>
    #cart-voucher-box { margin: 20px 0; }
    #cart-voucher-box .voucher-head { font-weight: 600; margin-bottom: 10px; }
    #cart-voucher-box .voucher-form { gap: 8px; margin-bottom: 12px; }
    #cart-voucher-box .voucher-form .input-text { flex: 1; min-width: 0; }
    #cart-voucher-box .voucher-code-apply,
    #cart-voucher-box .voucher-remove { border: 0; border-radius: 4px; padding: 0 16px; height: 40px; cursor: pointer; background: #1e88e5; color: #fff; white-space: nowrap; }
    #cart-voucher-box .voucher-remove { background: #eee; color: #333; height: 32px; }
    #cart-voucher-box .voucher-applied { display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 10px 12px; border: 1px dashed #2e7d32; background: #f1f8e9; border-radius: 4px; margin-bottom: 12px; }
    #cart-voucher-box .voucher-alert { padding: 8px 12px; background: #fff3e0; color: #b45309; border-radius: 4px; margin-bottom: 12px; }
    #cart-voucher-box .voucher-item { cursor: pointer; }
    #cart-voucher-box .voucher-item.voucher-disabled { opacity: .55; cursor: not-allowed; }
    #cart-voucher-box .voucher-reason { color: #b91c1c; }
    #cart-voucher-box .voucher-save { color: #2e7d32; }
    #cart-voucher-box .voucher-expire { color: #777; font-size: 12px; }
</style>
