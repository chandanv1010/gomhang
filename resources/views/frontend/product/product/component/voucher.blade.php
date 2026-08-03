@if(isset($voucher_product) && $price['price'] !== 0)
    <div class="product-info_voucher mb10">
        <div class="text_voucher">
            Mã giảm giá 
        </div>
        <div class="list-voucher">
            @foreach ($voucher_product as $k => $v)
                <div class="mini-coupon-item">
                    <a 
                        href="" 
                        class="info-voucher {{ $v->is_active == true ? 'active' : '' }}" 
                        data-voucher="{{ $v->id }}" 
                    >
                        Giảm {{ $v->discount_value }}{{ $v->discount_type == App\Enums\VoucherEnum::FIXED ? 'đ' : '%' }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif