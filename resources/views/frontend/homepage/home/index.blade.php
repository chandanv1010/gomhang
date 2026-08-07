@extends('frontend.homepage.layout')
@section('content')

<div class="uk-container uk-container-center home-content-container">
    
    <!-- Phần 2: Slide Banner nằm dưới header (nằm trong container) -->
    @php
        // Đọc từ slide "main-slide" trong admin. Trước đây đường dẫn ảnh ghi cứng
        // ở đây nên đổi ảnh trong admin không ăn thua gì ngoài trang chủ.
        $mainSlide = $slides[App\Enums\SlideEnum::MAIN] ?? null;
        $mainItems = [];
        if ($mainSlide) {
            $rawMain = is_array($mainSlide) ? ($mainSlide['item'] ?? '') : ($mainSlide->item ?? '');
            $mainItems = is_string($rawMain) ? json_decode($rawMain, true) : $rawMain;
        }
        $mainItems = array_filter((array) $mainItems, fn ($it) => !empty($it['image']));
    @endphp
    @if(!empty($mainItems))
    <div class="main-slide-container mb30">
        <div class="slide-wrapper">
            @foreach($mainItems as $item)
                <a href="{{ !empty($item['canonical']) ? $item['canonical'] : '#' }}"
                   target="{{ !empty($item['window']) ? $item['window'] : '_self' }}">
                    <img src="{{ $item['image'] }}"
                         alt="{{ $item['alt'] ?? ($item['name'] ?? '') }}"
                         class="slide-banner-img">
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Phần 3: Danh mục sản phẩm (Accessories Categories from Widget) - Hạn chế tối đa 12 danh mục -->
    @php
        $productWidget = $widgets['solution-product'] ?? null;
        $catalogues = (isset($productWidget->object) && $productWidget->object->isNotEmpty()) ? $productWidget->object : collect();
        // Separate the parent categories by ID
        $phuKienChungLoai = $catalogues->firstWhere('id', 4); // "Phụ kiện theo chủng loại"
        $phuKienIphone = $catalogues->firstWhere('id', 5); // "Phụ kiện iPhone"
        $phuKienSamsung = $catalogues->firstWhere('id', 6); // "Phụ kiện Samsung"
    @endphp

    <!-- Display "Phụ kiện theo chủng loại" as grid of circular icons (Hạn chế tối đa 12 danh mục) -->
    @if($phuKienChungLoai && !empty($phuKienChungLoai->childrens))
    <div class="panel-accessories-grid mb30">
        <div class="grid-container">
            @php
                // Lấy chính xác 12 danh mục có ID từ 7 đến 18 (theo thứ tự ảnh mẫu)
                $limitedChildrens = collect($phuKienChungLoai->childrens)
                    ->filter(function($c) {
                        $cid = is_array($c) ? ($c['id'] ?? 0) : ($c->id ?? 0);
                        return $cid >= 7 && $cid <= 18;
                    })
                    ->sortBy(function($c) {
                        $cid = is_array($c) ? ($c['id'] ?? 0) : ($c->id ?? 0);
                        return $cid;
                    });
            @endphp
            @foreach($limitedChildrens as $child)
                @php
                    $childName = is_array($child) ? ($child['languages']['name'] ?? '') : ($child->languages->name ?? '');
                    $childImage = is_array($child) ? ($child['image'] ?? '') : ($child->image ?? '');
                    $childCanonical = is_array($child) ? ($child['languages']['canonical'] ?? '') : ($child->languages->canonical ?? '');
                @endphp
                <a href="{{ write_url($childCanonical) }}" class="grid-item-link">
                    <div class="grid-item-circle">
                        <img src="{{ !empty($childImage) ? $childImage : '/userfiles/image/slide/logo.png' }}" alt="{{ $childName }}">
                    </div>
                    <span class="grid-item-label">{{ $childName }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Main Content Row: 3/4 Left & 1/4 Right -->
    <div class="home-main-section mb30">
        <!-- 3/4 Left Column: Sỉ lẻ hàng công nghệ -->
        <div class="left-content-3-4">
            @php
                $sileWidget = $widgets['si-le-cong-nghe'] ?? null;
                $products = (isset($sileWidget->object) && $sileWidget->object->isNotEmpty()) ? $sileWidget->object : collect();
            @endphp
            
            <div class="sile-widget-container">
                <div class="sile-widget-header">
                    <img src="/userfiles/image/slide/logo.png" alt="Icon" class="sile-icon-logo" onerror="this.style.display='none'">
                    {{-- This is the homepage's main heading, so it is the h1.
                         The page had no h1 at all. --}}
                    <h1>SỈ LẺ HÀNG CÔNG NGHỆ</h1>
                </div>

                <div class="products-grid-wrapper" id="products-grid">
                    @foreach($products as $product)
                        @php
                            $prodName = '';
                            $prodCanonical = '';
                            if (isset($product->languages)) {
                                if ($product->languages instanceof \Illuminate\Support\Collection) {
                                    $langObj = $product->languages->first();
                                    $prodName = $langObj->name ?? ($langObj->pivot->name ?? '');
                                    $prodCanonical = $langObj->canonical ?? ($langObj->pivot->canonical ?? '');
                                } else {
                                    $prodName = $product->languages->name ?? '';
                                    $prodCanonical = $product->languages->canonical ?? '';
                                }
                            }
                            // Same helper as the catalogue and detail pages so all
                            // three agree on the promotion price.
                            $priceInfo = getProductPriceInfo($product);
                            $originalPrice = $priceInfo['price'];
                            $salePrice = $priceInfo['priceSale'];
                            $percent = $priceInfo['percent'];
                        @endphp
                        <div class="product-grid-item" data-id="{{ $product->id }}">
                            <a href="{{ write_url($prodCanonical) }}" class="product-link">
                                <div class="product-image-box">
                                    <img src="{{ image($product->image) }}" alt="{{ $prodName }}" loading="lazy">
                                </div>
                                <div class="product-info-box">
                                    <h2 class="product-title">{{ $prodName }}</h2>
                                    
                                    <div class="product-price-row">
                                        <span class="product-sale-price">{{ convert_price($salePrice, true) }}đ</span>
                                        @if($percent > 0)
                                            <span class="product-discount-badge">Giảm {{ $percent }}%</span>
                                        @endif
                                    </div>
                                    @if($percent > 0)
                                        <div class="product-old-price-row">
                                            <span class="product-old-price">{{ convert_price($originalPrice, true) }}đ</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <!-- Load More Button -->
                <div class="load-more-btn-wrapper" id="load-more-wrapper">
                    <button id="load-more-btn" class="btn-load-more">TẢI THÊM CÁC SẢN PHẨM</button>
                </div>
            </div>
        </div>

        <!-- 1/4 Right Column: Sidebar -->
        <div class="right-sidebar-1-4">
            <!-- Box 1: Bảo hành giá -->
            <div class="sidebar-box warranty-box-container mb20">
                <div class="warranty-box-header">
                    <img src="/userfiles/image/slide/logo.png" alt="" class="warranty-header-icon" onerror="this.style.display='none'">
                    <h3>BẢO HÀNH GIÁ</h3>
                </div>
                <div class="warranty-box-body">
                    <div class="warranty-seal-img-box">
                        <img src="/userfiles/image/slide/warranty_seal.png" alt="Bảo hành giá" onerror="this.src='/userfiles/image/slide/logo.png'">
                    </div>
                    <div class="warranty-desc-content" style="text-align: left; font-size: 13px; line-height: 1.6; color: #444;">
                        <p style="margin-top: 0; font-weight: bold; color: #e01b24; text-align: center;">CAM KẾT 4 TỐT:</p>
                        <ul style="padding-left: 15px; margin: 0; list-style-type: disc;">
                            <li style="margin-bottom: 5px;"><b>Sản phẩm Tốt:</b> Nguồn gốc rõ ràng, chất lượng kiểm định kĩ càng.</li>
                            <li style="margin-bottom: 5px;"><b>Dịch vụ Tốt:</b> Giao hàng nhanh chóng, tư vấn tận tâm.</li>
                            <li style="margin-bottom: 5px;"><b>Bảo hành Tốt:</b> Lỗi 1 đổi 1 nhanh chóng, uy tín.</li>
                            <li style="margin-bottom: 5px;"><b>Giá thành Tốt:</b> Cam kết mức giá cạnh tranh nhất.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Box 2: Search form -->
            <div class="sidebar-box search-box-container mb20">
                <form action="/tim-kiem" method="GET" class="sidebar-search-form">
                    <button type="submit" class="sidebar-search-submit"><i class="fa fa-search"></i></button>
                    <input type="text" name="keyword" placeholder="Search..." value="{{ request('keyword') }}" class="sidebar-search-input">
                </form>
            </div>

            <!-- Box 3: Filter Category -->
            <div class="sidebar-box filter-box-container mb20">
                <h4 class="filter-box-title">Sản Phẩm</h4>
                <select class="sidebar-category-select-dropdown" onchange="if(this.value) window.location.href=this.value;">
                    <option value="">Chọn một danh mục</option>
                    @foreach($allCategories as $cat)
                        <option value="{{ write_url($cat->canonical) }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Box 4: Giới thiệu Gom -->
            <div class="sidebar-box intro-box-container">
                <div class="intro-box-header">
                    <img src="/userfiles/image/slide/logo.png" alt="" class="intro-header-icon" onerror="this.style.display='none'">
                    <h3>Giới thiệu {{ system_brand($system ?? null) }}</h3>
                </div>
                <div class="intro-box-body">
                    <!-- Social icons row -->
                    <div class="social-links-row mb15">
                        <a href="{{ $system['homepage_intro_youtube'] ?? '#' }}" target="_blank" class="social-btn youtube-btn">
                            <i class="fa fa-youtube-play"></i>
                            <span>YOUTUBE</span>
                        </a>
                        <a href="{{ $system['homepage_intro_tiktok'] ?? '#' }}" target="_blank" class="social-btn tiktok-btn">
                            <img src="/userfiles/image/slide/logo.png" alt="TikTok" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle; filter: brightness(0) invert(1);" onerror="this.style.display='none'">
                            <span>{{ system_website_label($system ?? null) }}</span>
                        </a>
                    </div>
                    <!-- Intro Storefront image -->
                    @if(!empty($system['homepage_intro_image']))
                        <div class="intro-banner-img-box">
                            <img src="{{ $system['homepage_intro_image'] }}" alt="Giới thiệu {{ system_brand($system ?? null) }}">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tin tức Gomhang.vn Section (Phía dưới) -->
    @php
        $newsWidget = $widgets['homepage-news'] ?? null;
        $newsCat = (isset($newsWidget->object) && $newsWidget->object->isNotEmpty()) ? $newsWidget->object->first() : null;
        $posts = $newsCat ? ($newsCat->posts ?? collect()) : collect();
    @endphp
    @if($posts->isNotEmpty())
    <div class="panel-homepage-news mb30">
        <div class="news-section-header">
            <img src="/userfiles/image/slide/logo.png" alt="" class="news-icon-logo" onerror="this.style.display='none'">
            <h2>TIN TỨC GOMHANG.VN</h2>
        </div>
        <div class="news-grid-container">
            @foreach($posts->take(6) as $post)
                @php
                    $postTitle = '';
                    $postCanonical = '';
                    if (isset($post->languages)) {
                        if ($post->languages instanceof \Illuminate\Support\Collection) {
                            $langObj = $post->languages->first();
                            $postTitle = $langObj->name ?? ($langObj->pivot->name ?? '');
                            $postCanonical = $langObj->canonical ?? ($langObj->pivot->canonical ?? '');
                        } else {
                            $postTitle = $post->languages->name ?? '';
                            $postCanonical = $post->languages->canonical ?? '';
                        }
                    }
                @endphp
                <div class="news-grid-item">
                    <a href="{{ write_url($postCanonical) }}" class="news-item-link">
                        <div class="news-image-box">
                            <img src="{{ $post->image }}" alt="{{ $postTitle }}" loading="lazy">
                        </div>
                        <h3 class="news-title-link">{{ $postTitle }}</h3>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Policy Banner Slider Section (4 slide images side by side) -->
    @php
        $policySlides = $slides['policy-slides'] ?? null;
        $policyItems = [];
        if ($policySlides) {
            $rawItem = is_array($policySlides) ? ($policySlides['item'] ?? '') : ($policySlides->item ?? '');
            $policyItems = is_string($rawItem) ? json_decode($rawItem, true) : $rawItem;
        }
    @endphp
    @if(!empty($policyItems))
    <div class="panel-policy-slides mb40">
        <div class="policy-slides-grid">
            @foreach($policyItems as $item)
                <div class="policy-slide-col">
                    <a href="{{ !empty($item['canonical']) ? $item['canonical'] : '#' }}" target="{{ !empty($item['window']) ? $item['window'] : '_self' }}">
                        <img src="{{ $item['image'] }}" alt="{{ $item['alt'] ?? '' }}" class="policy-slide-img">
                    </a>
                </div>
            @endforeach
        </div>
        <div class="policy-xem-them-btn-wrapper mt20">
            <a href="/chinh-sach-khach-hang.html" class="btn-policy-xem-them">Xem thêm: 8 chính sách – Quyền được an tâm</a>
        </div>
    </div>
    @endif

</div>

<style>
/* CSS Reset / Custom Override for GOMHANG 1420px max-width */
.uk-container,
.home-content-container {
    max-width: 1420px !important;
    margin: 0 auto;
    padding: 0 15px;
    box-sizing: border-box;
}

.mb15 { margin-bottom: 15px; }
.mb20 { margin-bottom: 20px; }
.mb30 { margin-bottom: 30px; }
.mb40 { margin-bottom: 40px; }
.mt20 { margin-top: 20px; }

/* Slide Banner styling */
.main-slide-container {
    width: 100%;
    overflow: hidden;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.slide-banner-img {
    width: 100%;
    height: auto;
    display: block;
}

/* Accessories titles */
.accessories-title-section {
    border-bottom: 1px solid #e2e2e2;
    margin-bottom: 20px;
    padding-bottom: 8px;
}
.accessories-title-section h3 {
    font-size: 16px;
    font-weight: bold;
    color: #333333;
    text-transform: none;
    margin: 0;
    font-family: 'Manrope', sans-serif;
}

/* Circular Grid styling (Image 5) */
.panel-accessories-grid {
    border-top: 1px solid #e5e5e5;
    border-left: 1px solid #e5e5e5;
    background-color: #ffffff;
    margin-bottom: 30px;
    border-radius: 4px;
    overflow: hidden;
}
.grid-container {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr));
    gap: 0;
    margin-bottom: 0;
}
@media (max-width: 767px) {
    .grid-container {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}
@media (max-width: 479px) {
    .grid-container {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
@media (max-width: 767px) {
    /* Square via aspect-ratio so the circle stays round while it shrinks. */
    .grid-item-circle {
        width: 100%;
        height: auto;
        aspect-ratio: 1 / 1;
    }
    .grid-item-link {
        padding: 16px 6px;
    }
}
.grid-item-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none !important;
    text-align: center;
    padding: 25px 10px;
    border-right: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    box-sizing: border-box;
    transition: background-color 0.2s;
}
.grid-item-link:hover {
    background-color: #fafafa;
}
/* Fixed at 110px this circle was wider than a grid column on a phone (3 columns
   of ~100px usable), and .panel-accessories-grid has overflow:hidden, so the
   right-hand column was silently clipped. Let it shrink to the column. */
.grid-item-circle {
    width: 110px;
    max-width: 100%;
    height: 110px;
    border-radius: 50%;
    background-color: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 8px;
    border: 1px solid #eeeeee;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.grid-item-link:hover .grid-item-circle {
    border-color: #e01b24;
    box-shadow: 0 3px 8px rgba(224, 27, 36, 0.12);
}
/* Fill the circle instead of a hard 110px. A fixed width here set the grid
   item's min-content width, and `1fr` means `minmax(auto, 1fr)`, so the columns
   could not shrink below it - which is what pushed the grid past the viewport. */
.grid-item-circle img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.grid-item-label {
    font-size: 12px;
    font-weight: bold;
    color: #1a1a1a;
    line-height: 1.3;
}
.grid-item-link:hover .grid-item-label {
    color: #e01b24;
}

/* Pill Tags Cloud styling */
.tags-cloud-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 10px;
}
.tag-pill-link {
    background-color: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 4px;
    padding: 5px 12px;
    color: #333333 !important;
    font-size: 13px;
    font-weight: bold;
    text-decoration: none !important;
    transition: all 0.2s;
}
.tag-pill-link:hover {
    border-color: #e01b24;
    color: #e01b24 !important;
    background-color: #fffaf9;
}

/* Main Section: 3/4 Left & 1/4 Right */
.home-main-section {
    display: flex;
    gap: 30px;
}
.left-content-3-4 {
    flex: 3;
    min-width: 0;
}
.right-sidebar-1-4 {
    flex: 1;
    min-width: 280px;
}
@media (max-width: 959px) {
    .home-main-section {
        flex-direction: column;
    }
    .left-content-3-4 {
        width: 100%;
    }
    .right-sidebar-1-4 {
        width: 100%;
    }
}

/* Widget Header styles */
.sile-widget-header,
.news-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 20px;
}
.sile-icon-logo,
.news-icon-logo {
    width: 28px;
    height: 28px;
    object-fit: contain;
}
.sile-widget-header h1,
.sile-widget-header h2,
.news-section-header h2 {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    font-family: 'Manrope', sans-serif;
    margin: 0;
}

/* Products Grid in Left Column */
.products-grid-wrapper {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border-top: 1px solid #eeeeee;
    border-left: 1px solid #eeeeee;
    margin-bottom: 25px;
}
@media (max-width: 1199px) {
    .products-grid-wrapper {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 767px) {
    .products-grid-wrapper {
        grid-template-columns: repeat(2, 1fr);
    }
}
.product-grid-item {
    border-right: 1px solid #eeeeee;
    border-bottom: 1px solid #eeeeee;
    background: #ffffff;
    box-sizing: border-box;
    padding: 15px;
    transition: box-shadow 0.2s;
}
.product-grid-item:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    position: relative;
    z-index: 10;
}
.product-link {
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.product-image-box {
    width: 100%;
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}
.product-image-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.product-info-box {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
}
.product-title {
    font-size: 13px;
    font-weight: 500;
    color: #1a1a1a;
    line-height: 1.4;
    margin: 0 0 10px 0;
    height: 38px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.product-grid-item:hover .product-title {
    color: #e01b24;
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
    color: #e01b24;
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

/* Load More Button styling */
.load-more-btn-wrapper {
    display: flex;
    justify-content: center;
    width: 100%;
    margin-top: 10px;
}
.btn-load-more {
    background-color: #eaeef3;
    color: #333 !important;
    border: none;
    padding: 14px 0;
    font-size: 13px;
    font-weight: bold;
    font-family: inherit;
    cursor: pointer;
    text-align: center;
    border-radius: 4px;
    width: 100%;
    transition: background-color 0.2s;
}
.btn-load-more:hover {
    background-color: #dbe3ed;
}

/* Right Sidebar boxes styling */
.sidebar-box {
    background: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 4px;
    padding: 15px;
    box-sizing: border-box;
}
.warranty-box-header,
.intro-box-header {
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #eaeaea;
    padding-bottom: 8px;
    margin-bottom: 15px;
}
.warranty-header-icon,
.intro-header-icon {
    width: 20px;
    height: 20px;
    object-fit: contain;
}
.warranty-box-header h3,
.warranty-box-header h4,
.intro-box-header h3,
.intro-box-header h4 {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin: 0;
}
.warranty-seal-img-box {
    display: flex;
    justify-content: center;
    margin-bottom: 15px;
}
.warranty-seal-img-box img {
    max-width: 180px;
    height: auto;
    object-fit: contain;
}
.warranty-desc-content {
    font-size: 12px;
    color: #444;
    line-height: 1.5;
    text-align: center;
}

/* Sidebar Search Box */
.sidebar-search-form {
    display: flex;
    align-items: center;
    border: 1px solid #cccccc;
    border-radius: 4px;
    overflow: hidden;
}
.sidebar-search-submit {
    border: none;
    background-color: #e01b24;
    color: #ffffff;
    padding: 8px 12px;
    cursor: pointer;
}
.sidebar-search-input {
    border: none;
    padding: 8px 10px;
    font-size: 13px;
    outline: none;
    flex-grow: 1;
}

/* Sidebar filter select box */
.filter-box-title {
    font-size: 13px;
    font-weight: bold;
    color: #333;
    margin: 0 0 10px 0;
}
.sidebar-category-select-dropdown {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #cccccc;
    border-radius: 4px;
    font-size: 13px;
    outline: none;
    background: #ffffff;
}

/* Giới thiệu Gom social buttons */
.social-links-row {
    display: flex;
    gap: 15px;
    justify-content: center;
}
.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50px;
    padding: 6px 15px;
    font-size: 11px;
    font-weight: bold;
    text-decoration: none !important;
    color: #ffffff !important;
    min-width: 110px;
}
.youtube-btn {
    background-color: #e32505;
}
.tiktok-btn {
    background-color: #000000;
}
.social-btn i {
    font-size: 14px;
    margin-right: 4px;
}
.intro-banner-img-box {
    margin-top: 15px;
    border-radius: 4px;
    overflow: hidden;
}
.intro-banner-img-box img {
    width: 100%;
    height: auto;
    display: block;
}

/* Tin tức Grid underneath */
.news-grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
@media (max-width: 959px) {
    .news-grid-container {
        grid-template-columns: repeat(2, 1fr);
    }
}
.news-item-link {
    text-decoration: none !important;
    display: block;
}
.news-image-box {
    width: 100%;
    aspect-ratio: 1.6;
    overflow: hidden;
    border-radius: 4px;
    margin-bottom: 10px;
    border: 1px solid #eeeeee;
}
.news-image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}
.news-item-link:hover .news-image-box img {
    transform: scale(1.05);
}
.news-title-link {
    font-size: 13px;
    font-weight: bold;
    color: #1a1a1a;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.news-item-link:hover .news-title-link {
    color: #e01b24;
}

/* Policy Slide Banners Section */
.policy-slides-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}
@media (max-width: 767px) {
    .policy-slides-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
.policy-slide-img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.policy-xem-them-btn-wrapper {
    display: flex;
    justify-content: center;
    width: 100%;
}
.btn-policy-xem-them {
    background-color: #ffffff;
    color: #333333 !important;
    border: 1px solid #7c7c7c;
    border-radius: 4px;
    padding: 6px 20px;
    font-size: 12px;
    font-weight: bold;
    text-decoration: none !important;
    transition: all 0.2s;
}
.btn-policy-xem-them:hover {
    background-color: #f7f7f7;
    border-color: #333333;
}
</style>

<script>
$(document).ready(function() {
    // Mechanism for lazy loading products in frontend (Max 60 initially, load 20 more on click)
    var visibleCount = 60;
    var $productItems = $('.product-grid-item');
    var totalCount = $productItems.length;

    // Initially hide all products beyond 60
    $productItems.each(function(index) {
        if (index >= visibleCount) {
            $(this).addClass('hidden-product').hide();
        }
    });

    // Hide load more button if total count is <= 60
    if (totalCount <= visibleCount) {
        $('#load-more-wrapper').hide();
    }

    // Handle click event on load more button
    $('#load-more-btn').on('click', function(e) {
        e.preventDefault();
        var $hiddenItems = $('.product-grid-item.hidden-product');
        var $toShow = $hiddenItems.slice(0, 20);
        
        $toShow.removeClass('hidden-product').fadeIn(300);

        // If no more hidden items, hide the button
        if ($('.product-grid-item.hidden-product').length === 0) {
            $('#load-more-wrapper').fadeOut(300);
        }
    });
});
</script>

@endsection
