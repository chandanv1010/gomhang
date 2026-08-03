<div class="panel-body">
    @if(count($carts) && !is_null($carts) )
        <div class="cart-list">
            @foreach($carts as $keyCart => $cart)
                <div class="cart-item" data-pd={{ $cart->id }} >
                    <div class="uk-grid uk-grid-medium"> 
                        <div class="uk-width-small-1-1 uk-width-medium-1-5">
                            <div class="cart-item-image">
                                <span class="image img-scaledown"><img src="{{ $cart->image }}" alt="{{ $cart->name }}"></span>
                                <span class="cart-item-number">{{ $cart->qty }}</span>
                            </div>
                        </div>
                        <div class="uk-width-small-1-1 uk-width-medium-4-5">
                            <div class="cart-item-info">
                                <h3 class="title"><span>{{ $cart->name }}</span></h3>
                                <div class="cart-item-action uk-flex uk-flex-middle uk-flex-space-between">
                                    <div class="cart-item-qty">
                                        <button type="button" class="btn-qty minus" {{ !is_null($cart->options->gifts) ? 'disabled' : '' }}>-</button>
                                        <input 
                                            type="text" 
                                            class="input-qty" 
                                            value="{{ $cart->qty }}"
                                            {{ !is_null($cart->options->gifts) ? 'readonly' : '' }}
                                        >
                                        <input type="hidden" class="rowId" value="{{ $cart->rowId }}">
                                        <button type="button" class="btn-qty plus" {{ !is_null($cart->options->gifts) ? 'disabled' : '' }}>+</button>
                                    </div>
                                    @php
                                        $discountVoucherForProduct  =  ($cart->status_combine == true) ? $cart->options->voucher['discount']  : 0;
                                    @endphp
                                    <div class="cart-item-price">
                                        <div class="uk-flex uk-flex-bottom">
                                            <span class="cart-price-old mr10">{{ convert_price($cart->priceOriginal * $cart->qty , true) }}đ</span>
                                            <span class="cart-price-sale">{{ convert_price($cart->price * $cart->qty - $discountVoucherForProduct * $cart->qty , true) }}đ</span>
                                            {{-- <span class="voucher-discount">-{{ convert_price($discountVoucherForProduct * $cart->qty, true) }}đ</span> --}}
                                        </div>
                                    </div>
                                    <div class="cart-item-remove" data-row-id="{{ $cart->rowId }}">
                                        <span>✕</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div>Bạn chưa có sản phẩm nào trong giỏ hàng <a style="color: red;" href="{{ write_url('san-pham') }}">Mua ngay</a></div>
    @endif
</div>