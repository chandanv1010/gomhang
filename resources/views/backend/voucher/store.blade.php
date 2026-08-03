@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('voucher.store') : route('voucher.update', $voucher->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-8">
                @include('backend.voucher.component.general',['model' => ($voucher) ?? null])
                @include('backend.voucher.component.detail')
            </div>
            @include('backend.voucher.component.aside',['model' => ($voucher) ?? null])
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
@include('backend.promotion.promotion.component.popup')
<input 
    type="hidden" 
    class="preload_promotionMethod" 
    value="{{ old('method', ($voucher->method) ?? null ) }}"
>
@if(isset($voucher))
    @php
        $module_type = $voucher->product_scope;
    @endphp
    @if($voucher->method == App\Enums\VoucherEnum::TOTAL_ORDERS)
        @php
            $voucher_total_order = [
                'max_discount_amount' => convert_price($voucher->max_discount_amount, true),
                'discount_value' => $voucher->discount_value,
                'discount_type' =>  $voucher->discount_type,
                'min_order_value' => convert_price($voucher->voucher_order_conditions->min_order_value, true),
                'max_order_value' => convert_price($voucher->voucher_order_conditions->max_order_value, true),
            ]
        @endphp
    @elseif($voucher->method == App\Enums\VoucherEnum::SHIPPING_ORDERS)
        @php
            $voucher_ship = [
                'max_discount_amount' => convert_price($voucher->max_discount_amount, true),
                'discount_value' => $voucher->discount_value,
                'discount_type' =>  $voucher->discount_type,
                'min_shipping_value' => convert_price($voucher->voucher_shipping_conditions->min_shipping_value, true),
                'max_shipping_value' => convert_price($voucher->voucher_shipping_conditions->max_shipping_value, true),
            ]

            // $voucher->{$dynamic}->{$dynamicValue}
        @endphp
    @elseif($voucher->method == App\Enums\VoucherEnum::PRODUCT_VOUCHER)
        @php
            $product_voucher = [
                'max_discount_amount' => convert_price($voucher->max_discount_amount, true),
                'discount_value' => $voucher->discount_value,
                'discount_type' =>  $voucher->discount_type,
            ]
        @endphp
    @endif
    
@endif
<input 
    type="hidden" 
    class="input_voucher_total_order" 
    value="{{ json_encode(old('TOTAL_ORDERS', ($voucher_total_order) ?? null )) }}"
>
<input 
    type="hidden" 
    class="input_voucher_ship" 
    value="{{ json_encode(old('SHIPPING_ORDERS', ($voucher_ship) ?? null )) }}"
>
<input 
    type="hidden"
    class="input_product_and_quantity"
    value="{{ json_encode(old('PRODUCT_VOUCHER', ($product_voucher) ?? null )) }}"
>
<input 
    type="hidden" 
    class="preload_select-product-and-quantity" 
    value="{{ old('module_type', ($module_type) ?? null ) }}"
>
<input 
    type="hidden"
    class="input_object"
    value="{{ json_encode(old('object' , ($object) ?? null )) }}"
>  