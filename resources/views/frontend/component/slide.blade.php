@php
    $slideKeyword = App\Enums\SlideEnum::MAIN;
@endphp

@if(!empty($slides[$slideKeyword]['item']))
    <div class="panel-slide page-setup" data-setting="">
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($slides[$slideKeyword]['item'] as $key => $val)
                    <div class="swiper-slide">
                        <div class="slide-inner uk-flex uk-flex-middle uk-flex-between">
                            {{-- Hình ảnh --}}
                            <div class="slide-image img-cover">
                                <img src="{{ $val['image'] }}" alt="{{ $val['name'] }}" />
                            </div>

                            {{-- Nội dung text --}}
                            <div class="slide-content wow fadeInLeft" data-wow-delay="0.3s">
                                <h2 class="slide-title">{{ $val['name'] }}</h2>

                                @if(!empty($val['alt']))
                                    <p class="slide-description">
                                        {{ $val['alt'] }}
                                    </p>
                                @endif

                                @if(!empty($val['canonical']))
                                    <a href="{{ $val['canonical'] }}" class="slide-btn">Xem thêm</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
