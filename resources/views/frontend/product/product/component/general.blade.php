
@if(!empty($product->iframe))
<div class="panel-product-detail mt30">
    <h2 class="heading-4"><span>Video</span></h2>
    <div class="productContent">
        {{ $product->iframe }}
    </div>
</div>
@endif

<div class="panel-product-detail mt30">
    <h2 class="heading-4 mb20"><span>Thông tin chi tiết</span></h2>
    <div class="productContent">
         {!! $product->content !!}
    </div>
</div>
