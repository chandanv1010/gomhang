@props(['class', 'name', 'number', 'canonical'])

<div class="slide-button uk-flex uk-flex-middle uk-flex-center {{ (isset($class)) ? $class : '' }}">
    <a href="{{ $canonical }}" class="uk-button button-primary uk-position-relative button-style-1">
        {{ $name }}
    </a>
    <div class="phone-call ml20  wow fadeInUp" data-wow-delay="0.4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
        <div class="phone-icon">
            <img src="/vendor/frontend/img/project/phone-call-1.png" alt="">
        </div>
        <div class="phone-info">
            <div class="phone-label">Hoặc gọi trực tiếp</div>
            <a href="tel:{{ $number }}" class="phone-number text-white">{{ $number }}</a>
        </div>
    </div>
</div>
