@extends('frontend.homepage.layout')
@section('content')
    <div class="cart-success">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-large-3-5">
                    <div class="order-success__information">
                        <h2 class="cart-success__heading">Cảm ơn Bạn đã đặt hàng tại hệ thống website: {{ $system['contact_website'] }}</h2>
                        <div class="cart-success__description">
                            <p class="mb50 order-success-code">
                                Mã đơn hàng của bạn: 
                                @foreach($orderSummary['orders'] as $keyOrderItem => $orderItem)
                                    <span class="order-item">{{ $orderItem->code }}{{ ($keyOrderItem + 1 < count($orderSummary['orders'])) ? ',' : '' }}</span>
                                @endforeach
                            </p>
                            <p class="order-success_note">
                                Đơn hàng của bạn đã được gửi đến chúng tôi nhưng hiện đang chờ thanh toán. Sau khi chúng tôi nhận được thanh toán cho đơn hàng của bạn, đơn hàng sẽ được hoàn tất. Nếu bạn đã cung cấp thông tin thanh toán, chúng tôi sẽ xử lý đơn hàng của bạn thủ công và gửi email thông báo khi đơn hàng được hoàn thành.
                            </p>

                            <div class="continue-shopping mt20">
                                <a href="{{ route("home.index") }}" class="btn btn-core" title="Tiếp tục mua sắm">Tiếp tục mua sắm</a>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="uk-width-large-2-5">
                    <div class="order-summary optimizedCheckout-orderSummary">
                        @if($orderSummary['orders'])
                            @foreach($orderSummary['orders'] as $order)
                            @php
                                $carts = $order->products;
                            @endphp
                            <div class="order-summary__item">
                                <h3 class="order-summary__heading">
                                    <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                        <span class="n">Thông tin đơn hàng</span>
                                        <span class="code">Mã đơn: {{ $order->code }}</span>
                                    </div>
                                </h3>
                               
                                <div class="order-summary__body">
                                    <div class="total-item mb20">{{ $order['cart']['cartTotalItems'] }} sản phẩm</div>
                                    <ul class="productList">
                                        @php
                                            $orderTotal = 0;
                                        @endphp
                                        @foreach($carts as $key => $val)
                                        @php
                                            $name = $val->pivot->name;
                                            $qty = $val->pivot->qty;
                                            $price = convert_price($val->pivot->price, true);
                                            $priceOriginal = convert_price($val->pivot->priceOriginal, true);
                                            $subtotal = convert_price($val->pivot->price * $qty, true);
                                            $discountVoucher = $order['cart']['cartVoucher'] ?? 0;
                                            $discountPromotion = $order['promotion']['discount'] ?? 0;
                                            $orderTotal += $val->pivot->price * $qty;
                                        @endphp
                                        <li class="productList-item">
                                            <div class="product-cart">
                                                <figure class="product-column product-figure">
                                                    <img alt="(Sample) Tempus sit amet tortor aliquam sagittis tincidunt" data-test="cart-item-image" src="{{ $val->image }}">
                                                </figure>
                                                <div class="product-column product-body">
                                                    <h4 class="product-title optimizedCheckout-contentPrimary" data-test="cart-item-product-title">{{ $qty }} x {{ $name }}</h4>
                                                </div>
                                                <div class="product-column product-actions">
                                                    <div class="product-price optimizedCheckout-contentPrimary" data-test="cart-item-product-price">{{ $subtotal }}đ</div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <section class="cart-section optimizedCheckout-orderSummary-cartSection">
                                    <div data-test="cart-subtotal">
                                        <div aria-live="polite" class="cart-priceItem optimizedCheckout-contentPrimary cart-priceItem--subtotal">
                                            <span class="cart-priceItem-label">
                                                <span data-test="cart-price-label">Tổng tạm  </span>
                                            </span>
                                            <span class="cart-priceItem-value">
                                                <span data-test="cart-price-value">{{ convert_price($orderTotal, true) }}đ</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div data-test="cart-subtotal">
                                        <div aria-live="polite" class="cart-priceItem optimizedCheckout-contentPrimary cart-priceItem--subtotal">
                                            <span class="cart-priceItem-label">
                                                <span data-test="cart-price-label">Giảm giá khuyến mại  </span>
                                            </span>
                                            <span class="cart-priceItem-value">
                                                <span class="voucher-value">-{{ convert_price($discountPromotion, true) }}đ</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div data-test="cart-subtotal">
                                        <div aria-live="polite" class="cart-priceItem optimizedCheckout-contentPrimary cart-priceItem--subtotal">
                                            <span class="cart-priceItem-label">
                                                <span data-test="cart-price-label">Giảm giá voucher  </span>
                                            </span>
                                            <span class="cart-priceItem-value">
                                                <span class="voucher-value">-{{ convert_price($discountVoucher, true) }}đ</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div data-test="cart-shipping">
                                        <div aria-live="polite" class="cart-priceItem optimizedCheckout-contentPrimary">
                                            <span class="cart-priceItem-label">
                                                <span data-test="cart-price-label">Phí Ship  </span>
                                            </span>
                                            <span class="cart-priceItem-value">
                                                <span data-test="cart-price-value">{{ convert_price($order->shipping, true) }}đ</span>
                                            </span>
                                        </div>
                                    </div>
                                </section>
                                <section class="cart-section optimizedCheckout-orderSummary-cartSection final-total">
                                    <div data-test="cart-total">
                                        <div aria-live="polite" class="cart-priceItem optimizedCheckout-contentPrimary cart-priceItem--total">
                                            <span class="cart-priceItem-label">
                                                <span data-test="cart-price-label">Tổng tiền</span>
                                            </span>
                                            <span class="cart-priceItem-value"><span data-test="cart-price-value">{{ convert_price($orderTotal - $discountVoucher - $discountPromotion + $order->shipping, true) }} đ</span>
                                        </span>
                                    </div>
                                </div>
                            </section>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

