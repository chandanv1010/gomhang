@if(isset($allVoucherTotal))
    <div class="panel-voucher">
        <div class="voucher-list">
            @foreach($allVoucherTotal as $k => $v)
                <div 
                    class="voucher-item {{ $v->coupon_use == true ? 'coupon-use' : '' }} {{ $v->is_active || (!is_null($voucher) && $v->id == $voucher['id']) ? 'use' : ''}}"
                    data-id = {{ $v->id }}
                    @if($v->product_scope == App\Enums\VoucherEnum::TOTAL_ORDERS)
                        data-min-order="{{ $v->min_order_value }}"
                    @elseif($v->product_scope == App\Enums\VoucherEnum::SHIPPING_ORDERS)
                        data-min-shipping="{{ $v->min_shipping_value }}"
                    @endif
                >
                    <div class="voucher-left"></div>
                    <div class="voucher-right">
                        <div class="voucher-title">{{ $v->code }} <span>(Còn {{ $v->total_quantity }})</span> </div>
                        <div class="voucher-description">
                            @if($v->product_scope == App\Enums\VoucherEnum::TOTAL_ORDERS || $v->product_scope == App\Enums\VoucherEnum::SHIPPING_ORDERS)
                                <p>
                                    Giảm {{ convertToK($v->discount_value) }}{{ $v->discount_type == App\Enums\VoucherEnum::FIXED ? 'đ' : '%'  }}
                                    cho {{ $v->min_order_value ? 'đơn' : 'phí ship' }}  từ 
                                    {{ convert_price($v->min_order_value ??  $v->min_shipping_value, true) }} đ tối đa {{ convert_price($v->max_discount_amount, true) }} đ
                                </p>
                            @else
                                <p>
                                    Giảm {{ convertToK($v->discount_value) }}{{ $v->discount_type == App\Enums\VoucherEnum::FIXED ? 'đ' : '%'  }} giá trị sản phẩm
                                    tối đa {{ convert_price($v->max_discount_amount, true) }} đ
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif