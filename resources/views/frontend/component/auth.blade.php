
@if(isset($customerAuth))
    <div class="header-top__widget">
        <div class="uk-flex uk-flex-middle">
            <i class="fi-rs-user"></i>
            <a href="{{ route('buyer.profile') }}" title="" class="widget-link">Xin chào: {{ $customerAuth->name }}</a>
            
        </div>
    </div>
@else
    <div class="header-top__widget">
        <div class="uk-flex uk-flex-middle">
            <i class="fi-rs-user"></i>
            <a href="{{ route('buyer.login') }}" title="" class="widget-link">Đăng nhập</a>
            <span>hoặc</span>
            <a href="{{ route('buyer.signup') }}" title="" class="widget-link">Tạo tài khoản</a>
        </div>
    </div>
@endif