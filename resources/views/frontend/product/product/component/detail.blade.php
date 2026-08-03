@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    $catName = $productCatalogue->name;
    $review = getReview($product);
    $description = $product->description;
    $attributeCatalogue = $product->attributeCatalogue;
    $gallery = json_decode($product->album);
    $iframe = $product->iframe;
    $total_lesson = !is_null($product->chapter) ? calculateCourses($product)['durationText'] : '';
@endphp
<div class="info">
    <div class="popup">
        <div class="uk-grid uk-grid-large">
            <div class="uk-width-large-1-2">
                <div class="popup-product">
                    <div class="badge">
                        <span>Product Description</span>
                    </div>
                    <h1 class="title product-main-title"><span>{{ $name }}</span></h1>
                    <div class="description">
                        {!! $description !!}
                    </div>
                    <div class="buttons">
                        {{-- <a href="" title="" class="btn btn-register"><span>Đăng ký ngay</span></a> --}}
                        {{-- <a href="" class="preview-video" data-video="{{ json_encode($product->iframe) }}" title="" class="btn btn-demo"><span>Watch Video</span></a> --}}
                    </div>
                </div>
            </div>
            <div class="uk-width-large-1-2">
                @php
                    $qrcode = $product->qrcode;
                @endphp
                @if(!empty($product->iframe))
                    <div class="video-feature product-video-feature p-r">
                        <div class="bg">
                            {!! $qrcode !!}
                        </div>
                        <a href="" data-video="{{ json_encode($product->iframe) }}" class="image img-cover wow fadeInUp video preview-video" data-wow-delay="0.2s" target="_blank" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <img src="{{ $image }}" alt="{{ $product->name }}">
                            <button class="btn-play">
                                <img src="/frontend/resources/img/play.svg" alt="">
                            </button>
                        </a>
                    </div>
                @else 
                    <a href="{{ $image }}" data-uk-lightbox="" class="image img-scaledown product-preview-image p-r">
                        <div class="bg">
                            {!! $qrcode !!}
                        </div>
                        <img src="{{ $image }}" alt="{{ $product->name }}">
                    </a>
                @endif
            </div>
        </div>
    </div>
    {{-- <div class="product-related mb30">
        <div class="uk-container uk-container-center">
            <div class="panel-product">
                <div class="main-heading">
                    <div class="panel-head">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <h2 class="heading-1"><span>Sản phẩm tương tự</span></h2>
                        </div>
                    </div>
                </div>
                <div class="panel-body list-product">
                    @if(count($productCatalogue->products))
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach($productCatalogue->products as $index => $product)
                                    <div class="swiper-slide">
                                        @include('frontend.component.product-item', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <div class="product-related product-view mb40">
        <div class="uk-container uk-container-center">
            <div class="panel-product">
                <div class="main-heading">
                    <div class="panel-head">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <h2 class="heading-1"><span>Sản phẩm đã xem</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(isset($widgets['recommend']))
        <div class="panel-recommend">
            <div class="panel-head">
                <h2 class="heading-1">
                    {{ $widgets['recommend']->name }}
                </h2>
            </div>
            @if(isset($widgets['recommend']->object))
                <div class="panel-body">
                    <div class="uk-grid uk-grid-small">
                        @foreach($widgets['recommend']->object as $k => $v)
                            <div class="uk-width-medium-1-4 mb6">
                                @php 
                                    $name = $v->languages->first()->pivot->name;
                                    $canonical = write_url($v->languages->first()->pivot->canonical);
                                @endphp
                                <a href="{{ $canonical }}" class="recommend-item"><span>{{ $name }}</span></a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif --}}
</div>

<input type="hidden" class="productName" value="{{ $product->name }}">
<input type="hidden" class="attributeCatalogue" value="{{ json_encode($attributeCatalogue) }}">
<input type="hidden" class="productCanonical" value="{{ write_url($product->canonical) }}">

