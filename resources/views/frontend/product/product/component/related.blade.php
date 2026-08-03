<div class="product-related mb20">
    <div class="panel-product">
        <div class="panel-head">
            <h2 class="heading-2"><span>Sản phẩm cùng danh mục</span></h2>
        </div>
        <div class="panel-body list-product">
            @if(count($productCatalogue->products))
            <div class="uk-grid uk-grid-medium">
                @foreach($productCatalogue->products as $index => $product)
                @if($index > 4) @break @endif
                <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                    @include('frontend.component.product-item', ['product' => $product])
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>