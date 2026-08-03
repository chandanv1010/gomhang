<div class="product-related">
    <div class="panel-product">
        <div class="panel-head">
            <h2 class="heading-2"><span>Sản phẩm đã xem</span></h2>
        </div>
        <div class="panel-body list-product">
            @if(!is_null($cartSeen) && isset($cartSeen) )
            <div class="uk-grid uk-grid-medium">
            @foreach($cartSeen as $key => $val)
            @php
                $name = $val->name;
                $canonical = $val->options['canonical'];
                $image = $val->options['image'];
                $priceSeen = number_format($val->price, 0, ',', '.');
            @endphp
                <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-5 mb20">
                    <div class="product-item product">
                        <a href="{{ $canonical }}" class="image img-scaledown img-zoomin"><img src="{{ $image }}" alt="{{ $name }}"></a>
                        <div class="info">
                            <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
                        </div> 
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>