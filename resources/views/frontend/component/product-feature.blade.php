@php
    $name = $product->languages->name;
    $canonical = write_url($product->languages->canonical);
    $image = image($product->image);
    // $price = getPrice($product);
    $catName = $product->product_catalogues->first()->languages->name;
    // $review = $product->review_average;
@endphp
<div class="product-item">
    <a href="{{ $canonical }}" title="{{ $name }}" class="image img-scaledown img-zoomin">
        <img  src="{{ $image }}" alt="{{ $name }}">
    </a>
    <div class="info">
        <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
        {{-- <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="product-price">
                {!! $price['html'] !!}
            </div>
        </div> --}}
    </div>
</div>