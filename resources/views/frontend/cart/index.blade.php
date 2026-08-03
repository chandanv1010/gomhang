@extends('frontend.homepage.layout')
@section('content')
<div class="cart-container">
    <div class="page-breadcrumb background">      
        <div class="uk-container uk-container-center">
            <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
                <li>
                    <a href="/" title="Trang chủ">Trang chủ</a>
                </li>
                <li>    
                    <span class="slash">/</span>
                </li>
                <li>
                    <a href="{{ write_url('thanh-toan') }}" title="Thanh toán">Thanh toán</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="uk-container uk-container-center">
        @if ($errors->any())
        <div class="uk-alert uk-alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('cart.store') }}" class="uk-form form" method="post">
            @csrf
            <h2 class="heading-1"><span>Thông tin đặt hàng</span></h2>
            <div class="cart-wrapper">
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-2-5">
                        <div class="panel-cart cart-left">
                            @include('frontend.cart.component.information')
                            @include('frontend.cart.component.method')
                        </div>
                    </div>
                    <div class="uk-width-large-3-5">
                        <div class="panel-cart">
                            <div class="panel-head">
                                <h2 class="cart-heading"><span>Đơn hàng</span></h2>
                            </div>
                            @include('frontend.cart.component.item')
                            @include('frontend.cart.component.voucher')
                             <div class="cart-point-box">
                                <div class="cart-point-head">
                                    <span class="label">Điểm tích luỹ hiện có:</span>
                                    <span class="value">{{ number_format($buyer->point) }} điểm</span>
                                </div>

                                <div class="cart-point-body">
                                    <label for="point_redeem">Nhập số điểm muốn sử dụng:</label>
                                    <input
                                        type="number"
                                        id="point_redeem"
                                        name="point_redeem"
                                        class="input-text"
                                        min="0"
                                        max="{{ $buyer->point }}"
                                        value="{{ old('point_redeem', 0) }}"
                                        placeholder="Nhập số điểm muốn đổi"
                                    >
                                    @error('point_redeem')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                    <p class="note">
                                        * Mỗi điểm tương ứng <strong>{{ number_format($pointRatio ?? 1) }}đ</strong>.
                                    </p>
                                </div>
                            </div>
                            @include('frontend.cart.component.summary')
                            @if(count($carts) && !is_null($carts) )
                                <button type="submit" class="cart-checkout" value="create" name="create">Thanh toán đơn hàng</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
<script>
    var province_id = '{{ (isset($order->province_id)) ? $order->province_id : old('province_id') }}'
    var district_id = '{{ (isset($order->district_id)) ? $order->district_id : old('district_id') }}'
    var ward_id = '{{ (isset($order->ward_id)) ? $order->ward_id : old('ward_id') }}'
</script>