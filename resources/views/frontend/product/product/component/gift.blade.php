@if(is_array($product['promotion_gifts']) && count($product['promotion_gifts']))
    <div class="product-promotion-take-gift">
        <div class="bg">
            <i class="fa fa-gift"></i> Khuyến mại
        </div>
        <div class="content">
            <ul>
                @foreach($product['promotion_gifts'] as $k => $item)
                    @php
                        $product_count = count($item['products']);
                        $product_gift_count = count($item['products']);
                        $type = $item['discount_information']['info']['model'];
                    @endphp
                    <li>
                        <div class="tooltip">
                            <input type="radio" name="type_promotion" value="{{ $type }}" id="{{ $k }}">
                            <i class="fa fa-gift"></i><label for="{{ $k }}">{{ $item['name'] }}</label>
                            <p class="tooltiptext">
                                Mua {{ $product_count }} SP
                                @foreach ($item['products'] as $product)
                                    <a href="{{ write_url($product['canonical']) }}">{{ $product['name'] }}</a> (x{{ $product['quantity'] }})
                                @endforeach
                                tặng {{ $product_gift_count }} SP 
                                @foreach ($item['product_gifts'] as $gift)
                                    <a href="{{ write_url($gift['canonical']) }}">{{ $gift['name'] }}</a> (x{{ $gift['quantity'] }})
                                @endforeach
                            </p>
                        </div>
                    </li> 
                @endforeach
            </ul>
        </div>
    </div>
@endif