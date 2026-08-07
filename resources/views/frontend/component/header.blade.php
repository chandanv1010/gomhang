<header class="gomhang-header">
    <div class="uk-container uk-container-center">
        <div class="header-main-container">
            <!-- Left: Logo -->
            <div class="header-left">
                <div class="logo">
                    <a href="/" title="{{ system_brand($system ?? null) }}">
                        @if(!empty($system['homepage_logo']))
                            <img src="{{ $system['homepage_logo'] }}" alt="Logo {{ system_brand($system ?? null) }}">
                        @else
                            <div class="logo-fallback">
                                <span class="logo-g">G</span><span class="logo-text">OMHANG.VN</span>
                            </div>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Center: Navigation -->
            <div class="header-center">
                <!-- Main nav -->
                {{-- Menu lấy từ Admin -> Menu -> nhóm "Menu chính". Trước đây 4 mục
                     và 3 mục con ghi cứng ở đây nên sửa menu trong admin không có
                     tác dụng gì. $menu do MenuComposer nạp sẵn cho mọi trang. --}}
                <nav class="main-navigation">
                    <ul class="main-menu-list">
                        @foreach($menu['main-menu_array'] ?? [] as $node)
                            @php
                                $lang = $node['item']->languages->first();
                                if (!$lang || !$lang->pivot) { continue; }
                                $name = $lang->pivot->name;
                                // canonical rỗng = trang chủ
                                $link = trim((string) $lang->pivot->canonical) === ''
                                    ? '/'
                                    : write_url($lang->pivot->canonical);
                                $children = $node['children'] ?? [];
                            @endphp
                            <li class="{{ count($children) ? 'has-dropdown' : '' }}">
                                <a href="{{ $link }}"
                                   class="menu-item {{ $link === '/' && request()->is('/') ? 'active' : '' }}">
                                    {{ mb_strtoupper($name) }}
                                    @if(count($children))<i class="fa fa-angle-down"></i>@endif
                                </a>
                                @if(count($children))
                                    <ul class="dropdown-menu-list">
                                        @foreach($children as $child)
                                            @php
                                                $childLang = $child['item']->languages->first();
                                            @endphp
                                            @if($childLang && $childLang->pivot)
                                                <li>
                                                    <a href="{{ write_url($childLang->pivot->canonical) }}">
                                                        {{ $childLang->pivot->name }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>

            <!-- Right: Search & Cart -->
            <div class="header-right">
                <!-- Top Row: Icons -->
                <div class="right-top-row">
                    {{-- Was a hardcoded /customer/login, which is a 404. Send signed-in
                         customers to their account instead of back to the login form. --}}
                    @if(auth()->guard('customer')->check())
                        <a href="{{ route('customer.account') }}" class="icon-link-item" title="Tài khoản của tôi">
                            <i class="fa fa-user-o"></i>
                        </a>
                    @else
                        <a href="{{ route('customer.login') }}" class="icon-link-item" title="Đăng nhập">
                            <i class="fa fa-user-o"></i>
                        </a>
                    @endif
                    <a href="{{ write_url('gio-hang') }}" class="icon-link-item cart-btn" title="Giỏ hàng">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="cart-badge">{{ Cart::instance('shopping')->count() }}</span>
                    </a>
                    <!-- Mobile Menu Trigger -->
                    <a class="mobile-menu-btn uk-hidden-large" href="#offcanvas" data-uk-offcanvas="{target:'#offcanvas'}">
                        <i class="fa fa-bars"></i>
                    </a>
                </div>

                <!-- Bottom Row: Search Box -->
                <div class="right-bottom-row">
                    <form action="{{ url('tim-kiem') }}" method="GET" class="search-form-container">
                        <input type="text" name="keyword" placeholder="Tìm kiếm..." value="{{ request('keyword') }}" class="search-input-field">
                        <button type="submit" class="search-submit-btn">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Offcanvas -->
<div id="offcanvas" class="uk-offcanvas">
    <div class="uk-offcanvas-bar mobile-menu-offcanvas">
        <button class="uk-offcanvas-close mobile-menu-close" type="button">
            <i class="fa fa-times"></i>
        </button>
        
        <div class="mobile-menu-header">
            <div class="mobile-menu-logo">
                <a href="/">
                    {{ mb_strtoupper(system_brand($system ?? null)) }}
                </a>
            </div>
        </div>

        <nav class="mobile-menu-nav">
            <ul class="uk-nav uk-nav-offcanvas mobile-menu-list">
                <li><a href="/">TRANG CHỦ</a></li>
                <li class="uk-parent">
                    <a href="{{ write_url('phu-kien-dien-thoai') }}">SẢN PHẨM</a>
                    <ul class="uk-nav-sub">
                        <li><a href="{{ write_url('phu-kien-theo-chung-loai') }}">Phụ kiện theo chủng loại</a></li>
                        <li><a href="{{ write_url('phu-kien-iphone') }}">Phụ kiện iPhone</a></li>
                        <li><a href="{{ write_url('phu-kien-samsung') }}">Phụ kiện Samsung</a></li>
                    </ul>
                </li>
                <li><a href="{{ write_url('tin-tuc-gomhang-vn') }}">KIẾN THỨC</a></li>
                <li><a href="{{ route('contact.index') }}">LIÊN HỆ</a></li>
            </ul>
        </nav>
    </div>
</div>

<style>
/* CSS styles to match Image 3 design */
.gomhang-header {
    background-color: #ffffff;
    border-bottom: none;
    padding: 15px 0;
    width: 100%;
    box-sizing: border-box;
    font-family: 'Manrope', sans-serif;
}
.logo img {
    width: 350px;
    height: auto;
    display: block;
}
.header-main-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

/* Left Section: Logo & Slogan */
.header-left {
    display: flex;
    align-items: center;
    flex: 1;
}
.logo-fallback {
    display: flex;
    align-items: center;
}
.logo-g {
    font-size: 38px;
    font-weight: bold;
    color: #000000;
    font-family: Georgia, serif;
    margin-right: 2px;
}
.logo-text {
    font-size: 22px;
    font-weight: 900;
    color: #000000;
    letter-spacing: -1px;
    margin-top: 8px;
}
.slogan-bubble {
    position: relative;
    border: 1px solid #7c7c7c;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 11px;
    color: #333333;
    font-weight: bold;
    margin-left: 20px;
    white-space: nowrap;
    background: #fff;
    box-shadow: 1px 1px 3px rgba(0,0,0,0.05);
}
.slogan-bubble::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
    width: 8px;
    height: 8px;
    background-color: #ffffff;
    border-left: 1px solid #7c7c7c;
    border-bottom: 1px solid #7c7c7c;
}

/* Center Section: Navigation */
.header-center {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 2;
}
.main-menu-list {
    display: flex;
    gap: 35px;
    list-style: none;
    padding: 0;
    margin: 0;
    align-items: center;
}
.main-menu-list > li {
    position: relative;
}
.menu-item {
    color: #000000 !important;
    font-weight: bold;
    font-size: 13px;
    text-decoration: none;
    text-transform: uppercase;
    padding-bottom: 4px;
    border-bottom: 2px solid transparent;
    transition: border-color 0.2s, color 0.2s;
    display: inline-block;
}
.menu-item:hover,
.menu-item.active {
    border-bottom: 2px solid #000000;
}
.has-dropdown:hover .dropdown-menu-list {
    display: block;
}
.dropdown-menu-list {
    display: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border: 1px solid #eeeeee;
    padding: 10px 0;
    list-style: none;
    margin: 0;
    min-width: 220px;
    z-index: 1000;
    border-radius: 4px;
}
.dropdown-menu-list li a {
    display: block;
    padding: 8px 20px;
    color: #333333 !important;
    font-size: 13px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.2s, color 0.2s;
}
.dropdown-menu-list li a:hover {
    background-color: #f7f7f7;
    color: #e01b24 !important;
}

/* Right Section: Icons & Search */
.header-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    flex: 1.2;
}
.right-top-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
}
/* The glyph is 24px, but the tappable box has to be bigger: measured at a phone
   viewport these links were only ~21x24, well under the 44px touch target. The
   box is invisible, so the header looks unchanged. */
.icon-link-item {
    color: #333333 !important;
    font-size: 24px;
    text-decoration: none;
    position: relative;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
}
.icon-link-item:hover {
    color: #e01b24 !important;
}
.cart-badge {
    position: absolute;
    /* Sits on the glyph's top-right corner, not the enlarged tap box's corner. */
    top: 4px;
    right: 4px;
    background-color: #e01b24;
    color: #ffffff;
    font-size: 10px;
    font-weight: bold;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.search-form-container {
    display: flex;
    align-items: center;
    border: 1px solid #cccccc;
    border-radius: 6px;
    overflow: hidden;
    width: 270px;
}
.search-input-field {
    border: none;
    padding: 10px 14px;
    font-size: 13px;
    flex-grow: 1;
    outline: none;
    font-family: inherit;
}
.search-submit-btn {
    border: none;
    background-color: #e01b24;
    color: #ffffff;
    padding: 10px 18px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s;
}
.search-submit-btn:hover {
    background-color: #b3151c;
}
/* The hamburger is the main navigation control on a phone and was measuring
   17x20 - the smallest tap target on the site. */
.mobile-menu-btn {
    font-size: 22px;
    color: #000 !important;
    cursor: pointer;
    margin-left: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
}

/* Mobile Offcanvas */
.mobile-menu-offcanvas {
    background: #ffffff !important;
    color: #333333 !important;
}
.mobile-menu-close {
    color: #333333 !important;
}
.mobile-menu-logo a {
    font-size: 24px;
    font-weight: bold;
    color: #000000 !important;
    text-decoration: none;
}
.mobile-menu-list li a {
    color: #333333 !important;
    font-weight: bold;
}

/* Sticky Header scroll support */
.gomhang-header.is-sticky {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1001;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    animation: headerSlideDown 0.3s ease-out;
}

@keyframes headerSlideDown {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

@media (max-width: 959px) {
    .header-center {
        display: none;
    }
    .slogan-bubble {
        display: none;
    }
    .right-bottom-row {
        display: none;
    }
}
</style>

<script>
    $(document).ready(function() {
        // Sticky Header scroll listener
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 120) {
                $('.gomhang-header').addClass('is-sticky');
            } else {
                $('.gomhang-header').removeClass('is-sticky');
            }
        });
    });
</script>