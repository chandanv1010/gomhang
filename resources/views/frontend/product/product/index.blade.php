@php
    // Chuẩn bị dữ liệu
    $prd_title = $product->name;
    $prd_code = $product->code;
    $prd_model = $product->model ?? '';

    $albumSource = is_array($product->album) ? $product->album : json_decode($product->album ?? '[]', true);
    $list_image = array_values(array_filter(is_array($albumSource) ? $albumSource : []));

    if (!empty($product->image)) {
        array_unshift($list_image, $product->image);
    }

    $list_image = array_values(array_unique($list_image));
    $prd_href = write_url($product->canonical ?? '');
    $prd_description = $product->description ?? '';
    $prd_extend_des = $product->content ?? '';
    
    $priceInfo = getProductPriceInfo($product);
    $stockQuantity = (int) ($product->stock ?? 0);
    $wishlistItems = isset($wishlist) ? $wishlist : collect();
    $wishlistIds = $wishlistItems->pluck('id')->toArray();
    $isWishlisted = in_array($product->id, $wishlistIds);

    // Flash sale runs off the promotion campaigns attached to this product.
    // There is deliberately no synthetic end date: showing a countdown when no
    // campaign exists advertises a deadline the shop is not committed to.
    $endDate = $priceInfo['endDate'];
    $promotionChain = $priceInfo['chain'];

    // Only worth rendering the block if there is a deadline to count down to, or
    // a later campaign to roll on to.
    $showFlashSale = !empty($endDate) || count($promotionChain) > ($priceInfo['hasPromotion'] ? 1 : 0);

    // Brand mark above the gallery, as in the reference design. getAttribute()
    // has already grouped the product's attributes by catalogue.
    $brandCatalogueId = (int) config('apps.general.brandAttributeCatalogueId');
    $brandAttribute = null;
    foreach (($product->attributeCatalogue ?? []) as $attrCatalogue) {
        if ((int) ($attrCatalogue->id ?? 0) !== $brandCatalogueId) {
            continue;
        }
        $brandAttribute = collect($attrCatalogue->attributes ?? [])->first();
        break;
    }
    $brandLogo = $brandAttribute ? brand_logo($brandAttribute) : null;
@endphp

@extends('frontend.homepage.layout')

@section('content')

    <div id="prddetail" class="page-body" style="background:#fff; font-family: 'Manrope', sans-serif;">
        
        <!-- Breadcrumbs bar -->
        <div class="cat-hero-section">
            <div class="uk-container uk-container-center cat-hero-container">
                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs" style="margin: 0; padding: 0;">
                    <li><a href="/">Trang chủ</a></li>
                    @if(!is_null($breadcrumb))
                        @foreach($breadcrumb as $key => $val)
                            @php
                                $name = $val->languages->first()->pivot->name;
                                $canonical = write_url($val->languages->first()->pivot->canonical, true, true);
                            @endphp
                            <li class="separator">&raquo;</li>
                            <li><a href="{{ $canonical }}">{{ $name }}</a></li>
                        @endforeach
                    @endif
                    <li class="separator">&raquo;</li>
                    <li><a href="#" onclick="return false;">{{ \Illuminate\Support\Str::limit($prd_title, 40) }}</a></li>
                </ul>
            </div>
        </div>

        <!-- Product Gallery & Info Section -->
        <section class="prddetail uk-margin-large-bottom">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium" data-uk-grid-margin>
                    
                    <!-- Left: Gallery & Vertical Features -->
                    <div class="uk-width-large-1-2">
                        <div class="uk-grid uk-grid-collapse">
                            <!-- Left: 4/5 Swiper -->
                            <div class="uk-width-4-5">
                                @if($brandLogo)
                                    <div class="prd-brand-mark">
                                        <img src="{{ $brandLogo }}" alt="{{ $brandAttribute->name ?? '' }}" loading="lazy">
                                    </div>
                                @endif
                                <div class="product-gallery">
                                    @if (isset($list_image) && !empty($list_image))
                                        <div class="product-list_image">
                                            <div class="swiper-container big-swiper" id="bigSwiper">
                                                <div class="swiper-wrapper big-pic">
                                                    @foreach($list_image as $val)
                                                        <div class="swiper-slide">
                                                            <a href="{{ $val }}" class="image img-cover img-v">
                                                                <img src="{{ image($val) }}" alt="{{ $prd_title }}">
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <!-- Thumbs Swiper -->
                                            <div class="swiper-container-thumbs thumb-swiper" style="margin-top: 15px;">
                                                <div class="swiper-wrapper pic-list" style="display: flex; gap: 8px;">
                                                    @foreach($list_image as $val)
                                                        <div class="swiper-slide" style="width: 60px; height: 60px; cursor: pointer; border: 1px solid #eee; border-radius: 4px; overflow: hidden;">
                                                            <span class="image img-cover"><img src="{{ image($val) }}" alt="Thumb"></span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Right: 1/5 Vertical Attribute Icons (Image 3) -->
                            <div class="uk-width-1-5 uk-flex uk-flex-column uk-flex-middle" style="gap: 20px; padding-left: 10px; box-sizing: border-box;">
                                @php
                                    $hasAttrs = false;
                                @endphp
                                @if(isset($product->attributeCatalogue) && !empty($product->attributeCatalogue))
                                    @foreach($product->attributeCatalogue as $cat)
                                        @foreach($cat->attributes as $attr)
                                            @php
                                                $name = $attr->name;
                                                $isFeature = str_contains(strtolower($name), 'amoled') 
                                                          || str_contains(strtolower($name), 'nghe gọi') 
                                                          || str_contains(strtolower($name), '3atm')
                                                          || str_contains(strtolower($name), 'chống nước')
                                                          || str_contains(strtolower($name), 'gps');
                                            @endphp
                                            @if($isFeature)
                                                @php $hasAttrs = true; @endphp
                                                <div class="vertical-feature-item uk-text-center">
                                                    <div class="feature-icon-circle">
                                                        @if(str_contains(strtolower($name), 'amoled'))
                                                            <i class="fa fa-tablet"></i>
                                                        @elseif(str_contains(strtolower($name), 'nghe gọi'))
                                                            <i class="fa fa-phone"></i>
                                                        @elseif(str_contains(strtolower($name), '3atm') || str_contains(strtolower($name), 'chống nước'))
                                                            <i class="fa fa-tint"></i>
                                                        @else
                                                            <i class="fa fa-check"></i>
                                                        @endif
                                                    </div>
                                                    <span class="feature-label">{{ $name }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                @endif
                                
                                @if(!$hasAttrs)
                                    <!-- Fallback icons from Image 3 -->
                                    <div class="vertical-feature-item uk-text-center">
                                        <div class="feature-icon-circle">
                                            <i class="fa fa-tablet"></i>
                                        </div>
                                        <span class="feature-label">Amoled</span>
                                    </div>
                                    <div class="vertical-feature-item uk-text-center">
                                        <div class="feature-icon-circle">
                                            <i class="fa fa-phone"></i>
                                        </div>
                                        <span class="feature-label">Nghe gọi</span>
                                    </div>
                                    <div class="vertical-feature-item uk-text-center">
                                        <div class="feature-icon-circle">
                                            <i class="fa fa-tint"></i>
                                        </div>
                                        <span class="feature-label">3ATM</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right: Product Info Column -->
                    <div class="uk-width-large-1-2">
                        <div class="product-info">
                            <!-- Title -->
                            <h1 class="prd-name" style="font-size: 20px; font-weight: 600; color: #1a1a1a; line-height: 1.35; margin-bottom: 6px;">{{ $prd_title }}</h1>
                            
                            {{-- The "Tag sản phẩm B" row that used to sit here was hardcoded
                                 placeholder markup, not product data, and the reference design
                                 has no such row. Removed rather than hidden. --}}

                            <!-- Price row -->
                            {{-- The ids let the flash sale script re-price in place when one
                                 campaign ends and the next takes over. --}}
                            <div class="prd-price-block uk-flex uk-flex-middle" style="gap: 10px; margin-bottom: 18px;">
                                <span class="price-highlight" id="prd-price-sale" style="color: #e02b1d !important; font-size: 23px; font-weight: 700; margin-left: 0;">
                                    {{ convert_price($priceInfo['priceSale'], true) }}đ
                                </span>
                                <span class="old-price" id="prd-price-old" style="text-decoration: line-through; color: #9a9a9a; font-size: 13px; @if($priceInfo['percent'] <= 0) display: none; @endif">
                                    {{ convert_price($priceInfo['price'], true) }}đ
                                </span>
                                <span class="discount-badge" id="prd-price-badge" style="border: 1px solid #cfcfcf; color: #333; background: #ffffff; padding: 3px 9px; border-radius: 4px; font-size: 12px; font-weight: 600; white-space: nowrap; @if($priceInfo['percent'] <= 0) display: none; @endif">
                                    Giảm {{ $priceInfo['percent'] }}%
                                </span>
                            </div>

                            <!-- FLASH SALE countdown timer boxes -->
                            @if($showFlashSale)
                                {{-- Layout follows the reference: the FLASH SALE label sits on the
                                     page background, and only the clock is boxed - one plain white
                                     panel, no per-digit tiles. --}}
                                <div class="flash-sale-container" id="flash-sale-container">
                                    <h2 class="flash-sale-title">FLASH SALE</h2>
                                    <div class="flash-sale-campaign" id="flash-sale-campaign">{{ $priceInfo['promotionName'] }}</div>
                                    <div class="countdown-shell">
                                        <div class="countdown-timer-wrapper" id="countdown-timer">
                                            <div class="timer-unit-box">
                                                <span class="timer-num" id="days">0</span>
                                                <span class="timer-label">Ngày</span>
                                            </div>
                                            <div class="timer-unit-box">
                                                <span class="timer-num" id="hours">00</span>
                                                <span class="timer-label">Giờ</span>
                                            </div>
                                            <div class="timer-unit-box">
                                                <span class="timer-num" id="minutes">00</span>
                                                <span class="timer-label">Phút</span>
                                            </div>
                                            <div class="timer-unit-box">
                                                <span class="timer-num" id="seconds">00</span>
                                                <span class="timer-label">Giây</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    /**
                                     * A product can sit in several promotion campaigns. The server sends
                                     * the whole sequence of segments - best discount first, each one
                                     * starting where the previous ended - so when a campaign expires the
                                     * page re-prices itself and counts down to the next one instead of
                                     * freezing on "Khuyến mãi kết thúc" until a reload.
                                     */
                                    document.addEventListener("DOMContentLoaded", function () {
                                        var chain = @json($promotionChain);
                                        var fullPrice = {{ (float) $priceInfo['price'] }};

                                        if (!Array.isArray(chain) || !chain.length) {
                                            return;
                                        }

                                        var el = {
                                            box: document.getElementById("flash-sale-container"),
                                            timer: document.getElementById("countdown-timer"),
                                            campaign: document.getElementById("flash-sale-campaign"),
                                            sale: document.getElementById("prd-price-sale"),
                                            old: document.getElementById("prd-price-old"),
                                            badge: document.getElementById("prd-price-badge"),
                                            days: document.getElementById("days"),
                                            hours: document.getElementById("hours"),
                                            minutes: document.getElementById("minutes"),
                                            seconds: document.getElementById("seconds")
                                        };

                                        // "2026-08-10 23:59:59" is not portable across browsers; slashes are.
                                        function stamp(value) {
                                            return value ? new Date(String(value).replace(/-/g, "/")).getTime() : null;
                                        }

                                        function money(value) {
                                            return Math.round(value).toLocaleString("vi-VN") + "đ";
                                        }

                                        function segmentAt(time) {
                                            for (var i = 0; i < chain.length; i++) {
                                                var startsAt = stamp(chain[i].startsAt);
                                                var endsAt = stamp(chain[i].endsAt);
                                                if (startsAt <= time && (endsAt === null || endsAt > time)) {
                                                    return chain[i];
                                                }
                                            }
                                            return null;
                                        }

                                        /** The next campaign due to start after $time, if any. */
                                        function nextSegmentAfter(time) {
                                            var soonest = null;
                                            for (var i = 0; i < chain.length; i++) {
                                                var startsAt = stamp(chain[i].startsAt);
                                                if (startsAt > time && (soonest === null || startsAt < stamp(soonest.startsAt))) {
                                                    soonest = chain[i];
                                                }
                                            }
                                            return soonest;
                                        }

                                        function showPrice(segment) {
                                            if (segment) {
                                                el.sale.innerText = money(segment.priceSale);
                                                el.old.innerText = money(segment.price);
                                                el.badge.innerText = "Giảm " + segment.percent + "%";
                                                el.old.style.display = "";
                                                el.badge.style.display = "";
                                            } else {
                                                el.sale.innerText = money(fullPrice);
                                                el.old.style.display = "none";
                                                el.badge.style.display = "none";
                                            }
                                        }

                                        function writeClock(distance) {
                                            var second = 1000, minute = 60 * second, hour = 60 * minute, day = 24 * hour;
                                            var pad = function (n) { return (n < 10 ? "0" : "") + n; };

                                            el.days.innerText = Math.floor(distance / day);
                                            el.hours.innerText = pad(Math.floor((distance % day) / hour));
                                            el.minutes.innerText = pad(Math.floor((distance % hour) / minute));
                                            el.seconds.innerText = pad(Math.floor((distance % minute) / second));
                                        }

                                        var shownSegmentId = null;

                                        function tick() {
                                            var now = Date.now();
                                            var active = segmentAt(now);

                                            // Re-price only when the winning campaign actually changes.
                                            var id = active ? active.promotion_id : null;
                                            if (id !== shownSegmentId) {
                                                shownSegmentId = id;
                                                showPrice(active);
                                                if (el.campaign) {
                                                    el.campaign.innerText = active ? (active.name || "") : "";
                                                }
                                            }

                                            if (active && stamp(active.endsAt) !== null) {
                                                writeClock(stamp(active.endsAt) - now);
                                                return;
                                            }

                                            if (active) {
                                                // Open ended campaign: a countdown would be meaningless.
                                                el.timer.innerHTML = "<div style='color:#d61c00; font-weight:bold;'>Áp dụng đến khi có thông báo mới</div>";
                                                return;
                                            }

                                            // Between campaigns: count down to the one that starts next.
                                            var upcoming = nextSegmentAfter(now);
                                            if (upcoming) {
                                                el.campaign.innerText = "Sắp diễn ra: " + (upcoming.name || "");
                                                writeClock(stamp(upcoming.startsAt) - now);
                                                return;
                                            }

                                            // Nothing left at all.
                                            clearInterval(handle);
                                            if (el.box) {
                                                el.box.style.display = "none";
                                            }
                                        }

                                        tick();
                                        var handle = setInterval(tick, 1000);
                                    });
                                </script>
                            @endif

                            <!-- Buy Action Box -->
                            <div class="buy-action-box">
                                {{-- Outlined cart button: icon with a small plus, and its own caption
                                     underneath, as in the reference. --}}
                                <a href="#" class="add-to-cart-badge-btn addToCart" data-id="{{ $product->id }}" aria-label="Thêm vào giỏ">
                                    <span class="cart-icon-stack">
                                        <i class="fa fa-shopping-cart"></i>
                                        <i class="fa fa-plus cart-icon-plus" aria-hidden="true"></i>
                                    </span>
                                    <span class="cart-btn-caption">Thêm vào giỏ</span>
                                </a>

                                <a href="#" class="buy-now-orange-btn addToCart" data-id="{{ $product->id }}" data-redirect="1">
                                    <span class="buy-now-title">MUA NHANH</span>
                                    <span class="buy-now-sub">(Giao nhanh hoặc nhận tại cửa hàng)</span>
                                </a>
                            </div>

                            <!-- Advisory Hotline numbers -->
                            <div class="prd-hotlines-box" style="margin-bottom: 25px; font-size: 14px; font-weight: 500; color: #333;">
                                <div class="hotline-line uk-flex uk-flex-middle" style="margin-bottom: 8px; gap: 8px;">
                                    <span style="display:inline-block; width: 22px; height: 22px; border-radius: 50%; background: #27ae60; color: #fff; text-align: center; line-height: 22px;"><i class="fa fa-phone" style="font-size: 12px;"></i></span>
                                    <span>Tư vấn, đặt hàng: <b style="color: #d61c00;">08.4224.6006</b> (8h-22h)</span>
                                </div>
                                <div class="hotline-line uk-flex uk-flex-middle" style="gap: 8px;">
                                    <span style="display:inline-block; width: 22px; height: 22px; border-radius: 50%; background: #27ae60; color: #fff; text-align: center; line-height: 22px;"><i class="fa fa-phone" style="font-size: 12px;"></i></span>
                                    <span>Hướng dẫn, hỗ trợ: <b style="color: #d61c00;">093.443.9055</b> (8h-22h)</span>
                                </div>
                            </div>

                            <!-- Shipping / Showrooms tabs box -->
                            <div class="prd-shipping-tabs-box">
                                <div class="shipping-tabs-header">
                                    <div class="shipping-tab active" id="tab-hn">Hà Nội</div>
                                    <div class="shipping-tab" id="tab-tq">Toàn Quốc</div>
                                </div>
                                <div class="shipping-tabs-content">
                                    <!-- Hanoi content -->
                                    <div id="content-hn">
                                        <p style="margin-top: 0; margin-bottom: 10px; font-weight: 500; color: #27ae60;">
                                            <i class="fa fa-truck"></i> Giao hàng miễn phí (từ 300k).
                                        </p>
                                        <ul style="list-style: none; padding: 0; margin: 0;">
                                            <li style="margin-bottom: 6px;"><i class="fa fa-home" style="color:#d61c00;"></i> Số 60 Hàng Đậu, Hoàn Kiếm <b style="color:#d61c00;">0929.460.868</b></li>
                                            <li style="margin-bottom: 6px;"><i class="fa fa-home" style="color:#d61c00;"></i> Số 21 Văn Cao, Ba Đình <b style="color:#d61c00;">0898.573.315</b></li>
                                            <li style="margin-bottom: 6px;"><i class="fa fa-home" style="color:#d61c00;"></i> Số 370 Xã Đàn, Đống Đa <b style="color:#d61c00;">0943.22.8888</b></li>
                                        </ul>
                                        <p style="margin-top: 10px; margin-bottom: 0;">
                                            <a href="/chinh-sach-khach-hang.html" style="color: #00509d; font-weight: bold; text-decoration: none;">Tham khảo 8 chính sách vàng Gomhang.vn &rarr;</a>
                                        </p>
                                    </div>
                                    <!-- National content -->
                                    <div id="content-tq" style="display: none;">
                                        <p style="margin-top: 0; margin-bottom: 10px; font-weight: 500; color: #27ae60;">
                                            <i class="fa fa-truck"></i> Giao hàng tận nhà nhanh chóng toàn quốc.
                                        </p>
                                        <p style="margin-bottom: 0;">
                                            - Phí ship đồng giá 25.000đ toàn quốc. Miễn phí vận chuyển cho đơn hàng từ 500.000đ.<br>
                                            - Nhận hàng được kiểm tra sản phẩm trước khi thanh toán.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var tabHn = document.getElementById("tab-hn");
                                    var tabTq = document.getElementById("tab-tq");
                                    var contentHn = document.getElementById("content-hn");
                                    var contentTq = document.getElementById("content-tq");
                                    
                                    // Toggle a class rather than writing inline colours, so the tab
                                    // appearance stays defined in one place (the stylesheet).
                                    if (tabHn && tabTq) {
                                        function selectTab(activeTab, inactiveTab, showContent, hideContent) {
                                            activeTab.classList.add("active");
                                            inactiveTab.classList.remove("active");
                                            showContent.style.display = "block";
                                            hideContent.style.display = "none";
                                        }

                                        tabHn.addEventListener("click", function () {
                                            selectTab(tabHn, tabTq, contentHn, contentTq);
                                        });
                                        tabTq.addEventListener("click", function () {
                                            selectTab(tabTq, tabHn, contentTq, contentHn);
                                        });
                                    }
                                });
                            </script>

                            <!-- Categories -->
                            @if(isset($productCatalogue->name))
                                <div class="prd-categories-row" style="font-size: 13px; color: #666;">
                                    Categories: <a href="{{ write_url($productCatalogue->canonical) }}" style="color: #666; font-weight: 500; text-decoration: none;">{{ $productCatalogue->name }}</a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Khối cam kết: 4 cái ảnh liền nhau nằm trong row (work/commit) -->
        <div class="prd-commitments-block uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
            <div class="commitments-row-container" style="border-top: 1px solid #eeeeee; border-bottom: 1px solid #eeeeee; padding: 25px 0; background: #ffffff;">
                <div class="uk-grid uk-grid-width-1-2 uk-grid-width-medium-1-4" data-uk-grid-margin style="text-align: center;">
                    <div>
                        <img src="/userfiles/image/commit/giao-hang-toan-quoc-2.jpg" alt="Giao hàng toàn quốc" style="max-height: 80px; display: inline-block;">
                    </div>
                    <div>
                        <img src="/userfiles/image/commit/8-ngay-doi-tra.webp" alt="8 ngày đổi trả" style="max-height: 80px; display: inline-block;">
                    </div>
                    <div>
                        <img src="/userfiles/image/commit/bao-hanh-1-2-nam.jpg" alt="Bảo hành 1-2 năm" style="max-height: 80px; display: inline-block;">
                    </div>
                    <div>
                        <img src="/userfiles/image/commit/support-tron-doi.jpg" alt="Support trọn đời" style="max-height: 80px; display: inline-block;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Content Details -->
        <div class="block-extend">
            <div class="uk-container uk-container-center">
                <div class="premium-detail-block">
                    <div class="detail-tab-header" style="border-bottom: 2px solid #eeeeee; padding-bottom: 8px; margin-bottom: 20px;">
                        <h2 class="detail-tab-title" style="font-size: 18px; font-weight: bold; color: #1e4794; margin: 0; position: relative; display: inline-block;">
                            MÔ TẢ CHI TIẾT SẢN PHẨM
                            <span style="position: absolute; bottom: -10px; left: 0; width: 100%; height: 3px; background: #d61c00;"></span>
                        </h2>
                    </div>
                    {{-- No inline padding here: an inline style beats the stylesheet, so
                         `padding: 20px 0` wiped out the horizontal padding and the description
                         ran flush against the card border. .detail-tab-body already sets
                         40px/35px, dropping to 25px/20px under 767px. --}}
                    <div class="detail-tab-body prd-shipping-policy prd-rich-content" style="font-size: 14px; line-height: 1.7; color: #444;">
                        {!! $product->content !!}
                    </div>
                </div>

                <!-- Related Products Section -->
                @if (isset($productRelated) && count($productRelated))
                    <section class="categories-panel uk-margin-large-top uk-margin-large-bottom">
                        <div class="related-header-section" style="border-bottom: 2px solid #eeeeee; padding-bottom: 8px; margin-bottom: 25px;">
                            <h2 class="detail-tab-title" style="font-size: 18px; font-weight: bold; color: #1e4794; margin: 0; position: relative; display: inline-block;">
                                SẢN PHẨM LIÊN QUAN
                                <span style="position: absolute; bottom: -10px; left: 0; width: 100%; height: 3px; background: #d61c00;"></span>
                            </h2>
                        </div>

                        {{-- No inline grid-template-columns here: an inline style beats the
                             stylesheet's media queries, so the related products stayed at four
                             columns on a phone and each card collapsed to ~75px. The
                             .products-grid-wrapper class already handles 4 / 2 / 1 columns. --}}
                        <div class="products-grid-wrapper">
                            @foreach ($productRelated as $valPost)
                                @php
                                    $rTitle = $valPost->name ?? '';
                                    $rCanonical = write_url($valPost->canonical);
                                    $rImage = image($valPost->image);
                                    $rPriceInfo = getProductPriceInfo($valPost);
                                @endphp
                                <div class="product-grid-item">
                                    <a href="{{ $rCanonical }}" class="product-link">
                                        <div class="product-image-box">
                                            <img src="{{ $rImage }}" alt="{{ $rTitle }}" loading="lazy">
                                        </div>
                                        <div class="product-info-box">
                                            <h4 class="product-title">{{ $rTitle }}</h4>
                                            
                                            <div class="product-price-row">
                                                <span class="product-sale-price">{{ convert_price($rPriceInfo['priceSale'], true) }}đ</span>
                                                @if($rPriceInfo['percent'] > 0)
                                                    <span class="product-discount-badge">Giảm {{ $rPriceInfo['percent'] }}%</span>
                                                @endif
                                            </div>
                                            @if($rPriceInfo['percent'] > 0)
                                                <div class="product-old-price-row">
                                                    <span class="product-old-price">{{ convert_price($rPriceInfo['price'], true) }}đ</span>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>

    </div>

<style>
/* CSS specific to Gallery & Icons (Mockup 3) */
.vertical-feature-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.feature-icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 1px solid #eeeeee;
    background-color: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 6px;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.vertical-feature-item:hover .feature-icon-circle {
    border-color: #d61c00;
    box-shadow: 0 4px 10px rgba(214, 28, 0, 0.08);
}
.feature-icon-circle i {
    font-size: 20px;
    color: #555;
}
.vertical-feature-item:hover .feature-icon-circle i {
    color: #d61c00;
}
.feature-label {
    font-size: 11px;
    font-weight: bold;
    color: #555555;
}

/* Category Hero Breadcrumbs Section */
.cat-hero-section {
    background-color: #f7f7f7;
    padding: 15px 0;
    border-bottom: 1px solid #eaeaea;
    margin-bottom: 25px;
}
.cat-hero-breadcrumbs {
    font-size: 13px;
    color: #666;
}
.cat-hero-breadcrumbs a {
    color: #666;
    text-decoration: none;
}
.cat-hero-breadcrumbs a:hover {
    color: #d61c00;
}
.cat-hero-breadcrumbs .separator {
    color: #ccc;
    margin: 0 8px;
}

/* Related/Products grid layout */
.products-grid-wrapper {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
@media (max-width: 959px) {
    .products-grid-wrapper {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 479px) {
    .products-grid-wrapper {
        grid-template-columns: 1fr;
    }
}

.product-grid-item {
    background-color: #ffffff;
    border: 1px solid #eaeaea;
    border-radius: 4px;
    overflow: hidden;
    transition: box-shadow 0.2s, border-color 0.2s;
    box-sizing: border-box;
}
.product-grid-item:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    border-color: #cccccc;
}
.product-link {
    display: block;
    text-decoration: none !important;
    color: inherit;
}
.product-image-box {
    width: 100%;
    aspect-ratio: 0.75;
    overflow: hidden;
    background-color: #fcfcfc;
    border-bottom: 1px solid #f2f2f2;
}
.product-image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.product-grid-item:hover .product-image-box img {
    transform: scale(1.03);
}
.product-info-box {
    padding: 15px;
}
.product-title {
    font-size: 13px;
    font-weight: 500;
    color: #00509d;
    line-height: 1.4;
    margin: 0 0 10px 0;
    height: 38px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.product-grid-item:hover .product-title {
    color: #d61c00;
}
.product-price-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}
.product-sale-price {
    font-size: 15px;
    font-weight: bold;
    color: #d61c00;
}
.product-discount-badge {
    border: 1px solid #000;
    border-radius: 4px;
    font-size: 10px;
    color: #333;
    font-weight: bold;
    padding: 2px 6px;
    white-space: nowrap;
}
.product-old-price-row {
    display: flex;
}
.product-old-price {
    font-size: 12px;
    color: #888888;
    text-decoration: line-through;
}

/* Shipping tab appearance lives further down in this stylesheet, in the block
   that matches the reference design. The rules that used to be here forced a
   white pill with !important, which no later rule could override. */

/* Swiper slider fit */
.big-swiper {
    width: 100%;
    border: 1px solid #eeeeee;
    border-radius: 4px;
    overflow: hidden;
}
.big-pic img {
    width: 100%;
    height: auto;
    object-fit: contain;
}

/* ------------------------------------------------------------------
   Price / flash sale / buy actions - styled to match the reference
   product page. Colours are read off the reference screenshot, so the
   exact hexes are close approximations rather than brand tokens.
   ------------------------------------------------------------------ */

/* --- Brand mark above the gallery ------------------------------- */
.prd-brand-mark {
    margin-bottom: 12px;
    padding-left: 2px;
}
.prd-brand-mark img {
    max-height: 20px;
    max-width: 110px;
    width: auto;
    height: auto;
    object-fit: contain;
    opacity: .85;
}

/* --- Flash sale ------------------------------------------------- */
.flash-sale-container {
    margin-bottom: 22px;
}
.flash-sale-title {
    margin: 0;
    color: #e02b1d;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: .4px;
    text-align: center;
    text-transform: uppercase;
}
.flash-sale-campaign {
    margin: 2px 0 8px;
    color: #9a9a9a;
    font-size: 12px;
    text-align: center;
}
.flash-sale-campaign:empty {
    display: none;
}
.countdown-shell {
    border: 1px solid #e6e6e6;
    border-radius: 6px;
    background: #fff;
    padding: 14px 8px;
}
.countdown-timer-wrapper {
    display: flex;
    justify-content: center;
}
.timer-unit-box {
    flex: 1 1 0;
    text-align: center;
}
.timer-num {
    display: block;
    color: #e02b1d;
    font-size: 26px;
    font-weight: 700;
    line-height: 1.15;
}
.timer-label {
    display: block;
    margin-top: 3px;
    color: #444;
    font-size: 13px;
}

/* --- Buy actions ------------------------------------------------ */
.buy-action-box {
    display: flex;
    align-items: stretch;
    gap: 12px;
    margin-bottom: 22px;
}
.add-to-cart-badge-btn {
    display: flex;
    flex: 0 0 74px;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    padding: 6px 4px;
    border: 1px solid #e02b1d;
    border-radius: 6px;
    background: #fff;
    color: #e02b1d !important;
    text-decoration: none !important;
    transition: background .2s, box-shadow .2s;
}
.add-to-cart-badge-btn:hover {
    background: #fff6f5;
    box-shadow: 0 2px 8px rgba(224, 43, 29, .15);
}
/* The plus sits on the cart glyph, so the two read as one mark. */
.cart-icon-stack {
    position: relative;
    display: inline-block;
    line-height: 1;
}
.cart-icon-stack .fa-shopping-cart {
    font-size: 22px;
}
.cart-icon-plus {
    position: absolute;
    top: -3px;
    right: -7px;
    font-size: 9px;
}
.cart-btn-caption {
    font-size: 9px;
    font-weight: 600;
    line-height: 1.1;
    text-align: center;
    white-space: nowrap;
}

.buy-now-orange-btn {
    display: flex;
    flex: 1 1 auto;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    padding: 10px 12px;
    border-radius: 6px;
    /* The reference button is a horizontal orange gradient, deeper on the left. */
    background: linear-gradient(90deg, #f4761b 0%, #fb8c3c 55%, #ff9c52 100%);
    color: #fff !important;
    text-align: center;
    text-decoration: none !important;
    transition: filter .2s;
}
.buy-now-orange-btn:hover {
    filter: brightness(1.05);
}
.buy-now-title {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: .3px;
}
.buy-now-sub {
    font-size: 12px;
    font-weight: 400;
}

/* --- Shipping tabs ---------------------------------------------- */
.prd-shipping-tabs-box {
    margin-bottom: 24px;
}
.shipping-tabs-header {
    display: flex;
    gap: 2px;
}
.shipping-tab {
    padding: 8px 22px;
    border-radius: 4px 4px 0 0;
    background: transparent;
    color: #666;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background .2s, color .2s;
}
.shipping-tab:hover {
    color: #0d6e5a;
}
.shipping-tab.active {
    background: #0d6e5a;
    color: #fff;
}
.shipping-tabs-content {
    padding: 14px 16px;
    background: #fafafa;
    border-radius: 0 4px 4px 4px;
    color: #444;
    font-size: 13.5px;
    line-height: 1.7;
}

/* ------------------------------------------------------------------
   Product description typography.

   The content is HTML written in the admin editor, and the theme's reset
   strips list markers and table borders, so an editor's bullet list and
   spec table rendered as flat unstyled text. These rules only apply
   inside the description block, so they cannot leak into the layout.
   ------------------------------------------------------------------ */
.prd-rich-content h2,
.prd-rich-content h3,
.prd-rich-content h4 {
    color: #1a1a1a;
    font-weight: 700;
    line-height: 1.35;
    margin: 26px 0 10px;
}
.prd-rich-content > *:first-child { margin-top: 0; }
.prd-rich-content h2 { font-size: 21px; }
.prd-rich-content h3 { font-size: 17px; }
.prd-rich-content h4 { font-size: 15px; }

.prd-rich-content p { margin: 0 0 12px; }
.prd-rich-content a { color: #d61c00; text-decoration: underline; }
.prd-rich-content strong { color: #222; }

.prd-rich-content ul,
.prd-rich-content ol {
    margin: 0 0 14px;
    padding-left: 22px;
}
/* The theme resets markers with `ul li { list-style: none }`. Setting
   list-style on the ul alone is not enough: list-style-type inherits, and an
   inherited value always loses to a rule that matches the li directly. So the
   marker has to be declared on the li itself. */
.prd-rich-content ul > li { list-style: disc outside; }
.prd-rich-content ol > li { list-style: decimal outside; }
.prd-rich-content li {
    display: list-item;
    margin-bottom: 6px;
    padding-left: 2px;
}
.prd-rich-content li::marker { color: #d61c00; }

.prd-rich-content table {
    width: 100%;
    margin: 0 0 16px;
    border-collapse: collapse;
    font-size: 13.5px;
}
.prd-rich-content th,
.prd-rich-content td {
    border: 1px solid #e4e4e4;
    padding: 9px 12px;
    text-align: left;
    vertical-align: top;
}
.prd-rich-content th {
    background: #f7f7f7;
    font-weight: 700;
    color: #222;
}
.prd-rich-content tbody tr:nth-child(even) td { background: #fbfbfb; }

.prd-rich-content blockquote {
    margin: 0 0 16px;
    padding: 12px 16px;
    border-left: 3px solid #d61c00;
    background: #fff8f7;
    color: #555;
}
.prd-rich-content blockquote p:last-child { margin-bottom: 0; }

.prd-rich-content code {
    background: #f3f4f6;
    border-radius: 3px;
    padding: 1px 5px;
    font-size: 12.5px;
    color: #b3261e;
}
.prd-rich-content img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

/* Wide spec tables must scroll inside the block, never widen the page. */
.prd-rich-content { overflow-x: auto; }
</style>

@endsection
