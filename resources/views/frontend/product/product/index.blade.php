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

    // Flash sale countdown end date configuration
    $endDate = $priceInfo['endDate'];
    if (empty($endDate) && $priceInfo['percent'] > 0) {
        $endDate = date('Y-m-t 23:59:59'); // Fallback to end of month for visual correctness
    }
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
                            <h1 class="prd-name" style="font-size: 24px; font-weight: bold; color: #111; margin-bottom: 8px;">{{ $prd_title }}</h1>
                            
                            <!-- Tags row -->
                            <div class="prd-tags-row uk-flex uk-flex-middle" style="gap: 8px; margin-bottom: 15px; font-size: 13px;">
                                <span style="color: #666; font-weight: 500;">Tag sản phẩm</span>
                                <span style="background: #2f80ed; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">B</span>
                            </div>

                            <!-- Price row -->
                            <div class="prd-price-block uk-flex uk-flex-middle" style="gap: 15px; margin-bottom: 20px;">
                                <span class="price-highlight" style="color: #d61c00 !important; font-size: 24px; font-weight: bold; margin-left: 0;">
                                    {{ convert_price($priceInfo['priceSale'], true) }}₫
                                </span>
                                @if($priceInfo['percent'] > 0)
                                    <span class="old-price" style="text-decoration: line-through; color: #888; font-size: 14px;">
                                        {{ convert_price($priceInfo['price'], true) }}₫
                                    </span>
                                    <span class="discount-badge" style="border: 1px solid #d61c00; color: #d61c00; background: #fff8f7; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                        Giảm {{ $priceInfo['percent'] }}%
                                    </span>
                                @endif
                            </div>

                            <!-- FLASH SALE countdown timer boxes -->
                            @if(!empty($endDate))
                                <div class="flash-sale-container" style="border: 1px solid #ffebe9; background: #fff9f8; border-radius: 6px; padding: 15px; margin-bottom: 20px;">
                                    <h4 class="flash-sale-title" style="color: #d61c00; font-weight: bold; text-align: center; text-transform: uppercase; margin: 0 0 12px 0; font-size: 15px; letter-spacing: 0.5px;">FLASH SALE</h4>
                                    <div class="countdown-timer-wrapper" id="countdown-timer" style="display: flex; justify-content: center; gap: 15px;">
                                        <div class="timer-unit-box" style="text-align: center;">
                                            <span class="timer-num-box" id="days" style="display: block; width: 45px; height: 45px; line-height: 45px; font-size: 20px; font-weight: bold; color: #d61c00; background: #ffffff; border: 1px solid #ffd1cd; border-radius: 4px;">0</span>
                                            <span class="timer-label" style="font-size: 11px; color: #666; margin-top: 4px; display: block;">Ngày</span>
                                        </div>
                                        <div class="timer-unit-box" style="text-align: center;">
                                            <span class="timer-num-box" id="hours" style="display: block; width: 45px; height: 45px; line-height: 45px; font-size: 20px; font-weight: bold; color: #d61c00; background: #ffffff; border: 1px solid #ffd1cd; border-radius: 4px;">00</span>
                                            <span class="timer-label" style="font-size: 11px; color: #666; margin-top: 4px; display: block;">Giờ</span>
                                        </div>
                                        <div class="timer-unit-box" style="text-align: center;">
                                            <span class="timer-num-box" id="minutes" style="display: block; width: 45px; height: 45px; line-height: 45px; font-size: 20px; font-weight: bold; color: #d61c00; background: #ffffff; border: 1px solid #ffd1cd; border-radius: 4px;">00</span>
                                            <span class="timer-label" style="font-size: 11px; color: #666; margin-top: 4px; display: block;">Phút</span>
                                        </div>
                                        <div class="timer-unit-box" style="text-align: center;">
                                            <span class="timer-num-box" id="seconds" style="display: block; width: 45px; height: 45px; line-height: 45px; font-size: 20px; font-weight: bold; color: #d61c00; background: #ffffff; border: 1px solid #ffd1cd; border-radius: 4px;">00</span>
                                            <span class="timer-label" style="font-size: 11px; color: #666; margin-top: 4px; display: block;">Giây</span>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        var endDateStr = "{{ $endDate }}";
                                        if (endDateStr) {
                                            var countDownDate = new Date(endDateStr.replace(/-/g, "/")).getTime();
                                            var x = setInterval(function() {
                                                var now = new Date().getTime();
                                                var distance = countDownDate - now;
                                                
                                                if (distance < 0) {
                                                    clearInterval(x);
                                                    document.getElementById("countdown-timer").innerHTML = "<div class='expired-msg' style='color:#777; font-weight:bold;'>Khuyến mãi kết thúc</div>";
                                                    return;
                                                }
                                                
                                                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                
                                                document.getElementById("days").innerText = days;
                                                document.getElementById("hours").innerText = (hours < 10 ? "0" : "") + hours;
                                                document.getElementById("minutes").innerText = (minutes < 10 ? "0" : "") + minutes;
                                                document.getElementById("seconds").innerText = (seconds < 10 ? "0" : "") + seconds;
                                            }, 1000);
                                        }
                                    });
                                </script>
                            @endif

                            <!-- Buy Action Box -->
                            <div class="buy-action-box uk-flex uk-flex-middle" style="gap: 15px; margin-bottom: 25px;">
                                <!-- Add to Cart badge button (red boundary) -->
                                <a href="#" class="add-to-cart-badge-btn addToCart" data-id="{{ $product->id }}" style="width: 50px; height: 50px; border: 1px solid #d61c00; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #d61c00; text-decoration: none !important; transition: all 0.2s;">
                                    <i class="fa fa-shopping-cart" style="font-size: 24px;"></i>
                                </a>
                                
                                <!-- Mua Nhanh orange button -->
                                <a href="#" class="buy-now-orange-btn addToCart" data-id="{{ $product->id }}" data-redirect="1" style="flex-grow: 1; background: #ff7f16; color: #ffffff !important; border-radius: 4px; text-align: center; padding: 12px 10px; font-weight: bold; font-size: 14px; text-decoration: none !important; transition: all 0.2s; line-height: 1.3;">
                                    MUA NHANH<br>
                                    <span style="font-size: 11px; font-weight: normal; opacity: 0.9;">(Giao nhanh hoặc nhận tại cửa hàng)</span>
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
                            <div class="prd-shipping-tabs-box" style="background: #fdfdfd; border: 1px solid #eeeeee; border-radius: 6px; overflow: hidden; margin-bottom: 25px;">
                                <div class="shipping-tabs-header uk-flex" style="background: #f5f5f5; border-bottom: 1px solid #eeeeee;">
                                    <div class="shipping-tab active" id="tab-hn" style="flex: 1; text-align: center; padding: 10px; font-weight: bold; cursor: pointer; background: #ffffff; border-right: 1px solid #eeeeee;">Hà Nội</div>
                                    <div class="shipping-tab" id="tab-tq" style="flex: 1; text-align: center; padding: 10px; font-weight: bold; cursor: pointer; color: #666;">Toàn Quốc</div>
                                </div>
                                <div class="shipping-tabs-content" style="padding: 15px; font-size: 13px; line-height: 1.6; color: #444;">
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
                                    
                                    if (tabHn && tabTq) {
                                        tabHn.addEventListener("click", function() {
                                            tabHn.style.background = "#ffffff";
                                            tabHn.style.color = "#333";
                                            tabTq.style.background = "transparent";
                                            tabTq.style.color = "#666";
                                            contentHn.style.display = "block";
                                            contentTq.style.display = "none";
                                        });
                                        tabTq.addEventListener("click", function() {
                                            tabTq.style.background = "#ffffff";
                                            tabTq.style.color = "#333";
                                            tabHn.style.background = "transparent";
                                            tabHn.style.color = "#666";
                                            contentTq.style.display = "block";
                                            contentHn.style.display = "none";
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
                    <div class="detail-tab-body prd-shipping-policy" style="padding: 20px 0; font-size: 14px; line-height: 1.7; color: #444;">
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

                        <div class="products-grid-wrapper" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
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
                                                <span class="product-sale-price">{{ convert_price($rPriceInfo['priceSale'], true) }}₫</span>
                                                @if($rPriceInfo['percent'] > 0)
                                                    <span class="product-discount-badge">Giảm {{ $rPriceInfo['percent'] }}%</span>
                                                @endif
                                            </div>
                                            @if($rPriceInfo['percent'] > 0)
                                                <div class="product-old-price-row">
                                                    <span class="product-old-price">{{ convert_price($rPriceInfo['price'], true) }}₫</span>
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

/* Shipping tabs styles */
.shipping-tab:hover {
    color: #d61c00 !important;
}
.shipping-tab.active {
    background: #ffffff !important;
    color: #333 !important;
    border-top: 2px solid #d61c00;
}

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
</style>

@endsection
