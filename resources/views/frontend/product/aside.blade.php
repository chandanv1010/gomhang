<div class="aside uk-height-1-1">
    @if($category && count($category))
        @foreach($category as $key => $val)
        @php
            $categoryName = $val['item']->languages->first()->pivot->name;
        @endphp
        <div class="aside-category panel-aside">
            <div class="aside-head">
                <h5>Danh mục {{ $categoryName }}</h5>
            </div>
            @if($val['children'] && count($val['children']))
            <div class="aside-body">
                <div class="uk-accordion" data-uk-accordion="{collapse: false}">
                    @foreach($val['children'] as $keyChild => $valChild)
                    @php
                        $childrenName = $valChild["item"]->languages->first()->pivot->name;
                        $childrenCanonical = write_url($valChild["item"]->languages->first()->pivot->canonical);
                    @endphp
                    <div class="uk-accordion-item">
                        <h3 class="uk-accordion-title">
                            <a href="{{ $childrenCanonical }}"> {{ $childrenName }}</a>
                            <span><i class="fi-rs-angle-right"></i></span>
                        </h3>
                       
                        <div class="uk-accordion-content">
                            @if($valChild['children'] && count($valChild['children']))
                            <ul class="ul-list uk-clearflix">
                                @foreach($valChild['children'] as $children)
                                @php
                                    $name = $children['item']->languages->first()->pivot->name;
                                    $canonical = write_url($children['item']->languages->first()->pivot->canonical);
                                @endphp
                                <li><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                   </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>    
        @endforeach 
    @endif

    @if($widgets['suggest'])
    <div class="aside-feature panel-aside">
        <div class="aside-head">
            <h5>{{ $widgets['suggest']->name }}</h5>
        </div>
        <div class="aside-body">
            @if($widgets['suggest']->object && count($widgets['suggest']->object))
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($widgets['suggest']->object as $product)
                        <div class="swiper-slide">
                            @include('frontend.component.product-item', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if(isset($widgets['bestseller-home']))
    <div class="aside-feature panel-aside" data-uk-sticky="{boundary: true, top:10}">
        <div class="aside-head">
            <h5>{{ $widgets['bestseller-home']->name }}</h5>
        </div>
        <div class="aside-body">
            @if($widgets['bestseller-home']->object && count($widgets['bestseller-home']->object))
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($widgets['bestseller-home']->object as $product)
                        <div class="swiper-slide">
                            @include('frontend.component.product-item', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

</div>