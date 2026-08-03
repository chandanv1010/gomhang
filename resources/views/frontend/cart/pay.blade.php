@extends('frontend.homepage.layout')
@section('content')
    @php
        $price = getPrice($product)
    @endphp
    <div id="suggest" class="panel-suggest pay">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-medium-1-3">
                    <div class="info-product">
                        <h3 class="heading-2">
                            <span>{{ $product->languages->first()->pivot->name }}</span>
                            <div class="border-remark"></div>
                        </h3>
                        <a href="" class="image img-cover">
                            <img src="{{ $product->image }}" alt="">
                        </a>
                        <div class="product-price">
                            {!! $price['html'] ?? null !!}
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-2-3">
                    @if ($errors->any())
                    <div class="uk-alert uk-alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('cart.storePay') }}" class="info-form" method="post">
                        @csrf
                        <div class="panel-head">
                            <h2 class="heading-1">
                                Nhập thông tin của bạn
                                <div class="border-remark"></div>
                            </h2>
                            <p>(Để chúng tôi phục vụ bạn chu đáo hơn )</p>
                        </div>
                        <div class="panel-body">
                            <div class="up mb20">
                                <div class="fr">
                                    <input type="text" name="fullname" placeholder="Tên quý khách" class="mr10">
                                    <span class="error-message name-error"></span>
                                </div>
                                <div class="flex items-center gap-2 border px-3 bg-light w-fit">
                                    <div class="flex items-center gap-2 uk-flex uk-flex-middle">
                                        <input id="male" type="radio" value="male" checked="" name="gender" class="" >
                                        <label for="male">Anh</label>
                                        <input id="female" type="radio" value="female" checked="" name="gender" class="" >
                                        <label for="female">Chị</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row mb20">
                                <input placeholder="Số điện thoại" name="phone" value="" class="border h-10 text-sm p-2.5 w-full" on:input="q-D8CXyDHq.js#s_WQShqIriXzI[0 1 2 3]" on:change="q-D8CXyDHq.js#s_vNtVq2dMPhY[0 1 2]" on:blur="q-D8CXyDHq.js#s_fzfym1ErEFI[0 1 2]" q:id="8m">
                                <span class="error-message phone-error"></span>
                            </div>
                            <div class="form-row mb20">
                                <input placeholder="Địa chỉ" name="address" value="" class="border h-10 text-sm p-2.5 w-full" on:input="q-D8CXyDHq.js#s_WQShqIriXzI[0 1 2 3]" on:change="q-D8CXyDHq.js#s_vNtVq2dMPhY[0 1 2]" on:blur="q-D8CXyDHq.js#s_fzfym1ErEFI[0 1 2]" q:id="8q">
                            </div>
                            <div class="sys mb20">
                                @if(isset($widgets['showroom-system']))
                                    @foreach ($widgets['showroom-system']->object as $item)
                                        <div class="showroom-sys">
                                            <p>Chọn showroom gần bạn nhất:</p>
                                            @foreach ($item->posts  as $key => $post)
                                                <div class="sys-item">
                                                    <input id="{{ $post->id }}" type="radio" value="{{ $post->id }}" checked="" name="post_id" class="">
                                                    <label for="{{ $post->id }}">{{ $post->languages->first()->pivot->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="method showroom-sys mb20">
                                <p>Chọn phương thức thanh toán :</p>
                                <div class="sys-item">
                                    <input type="radio" name="method" value="cod" checked="" id="cod">
                                    <label for="code">Thanh toán khi nhận hàng</label>
                                </div>
                            </div>
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="type" value="2">
                            <button type="" class="advise">Mua hàng</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection