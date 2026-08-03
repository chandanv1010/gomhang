@extends('frontend.homepage.layout')

@section('content')
    <div id="prd-catalogue" class="page-body">
        
        <!-- Breadcrumb & Title Section -->
        <div class="cat-hero-section">
            <div class="uk-container uk-container-center cat-hero-container">
                <h1 class="cat-hero-title">{{ $productCatalogue->name }}</h1>
                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs">
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
                </ul>
            </div>
        </div>

        <!-- Description Box -->
        @if(!empty($productCatalogue->description))
            <div class="cat-description-box uk-container uk-container-center uk-text-center">
                {!! $productCatalogue->description !!}
            </div>
        @endif

        @php
            $isRootCategory = ($productCatalogue->parent_id == 0 || $productCatalogue->canonical === 'san-pham' || $productCatalogue->id == 65 || $productCatalogue->id == 4);
        @endphp

        <!-- Root Category Child Directories (Only for root category "Sản Phẩm" / "Phụ kiện điện thoại") -->
        @if($isRootCategory)
            <div class="uk-container uk-container-center root-category-sections uk-margin-large-bottom">
                
                <!-- Phụ kiện theo chủng loại -->
                @if(isset($chungLoaiList) && count($chungLoaiList))
                    <div class="root-section-block">
                        <h3 class="root-section-title">Phụ kiện theo chủng loại</h3>
                        <div class="root-tags-cloud">
                            @foreach($chungLoaiList as $item)
                                @php
                                    $cName = $item->languages->first()->pivot->name ?? $item->name;
                                    $cCanonical = write_url($item->languages->first()->pivot->canonical ?? $item->canonical);
                                @endphp
                                <a href="{{ $cCanonical }}" class="tag-pill-link">
                                    {{ $cName }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Phụ kiện iPhone -->
                @if(isset($iphoneList) && count($iphoneList))
                    <div class="root-section-block">
                        <h3 class="root-section-title">Phụ kiện iPhone</h3>
                        <div class="root-tags-cloud">
                            @foreach($iphoneList as $item)
                                @php
                                    $cName = $item->languages->first()->pivot->name ?? $item->name;
                                    $cCanonical = write_url($item->languages->first()->pivot->canonical ?? $item->canonical);
                                @endphp
                                <a href="{{ $cCanonical }}" class="tag-pill-link">
                                    {{ $cName }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Phụ kiện Samsung -->
                @if(isset($samsungList) && count($samsungList))
                    <div class="root-section-block">
                        <h3 class="root-section-title">Phụ kiện Samsung</h3>
                        <div class="root-tags-cloud">
                            @foreach($samsungList as $item)
                                @php
                                    $cName = $item->languages->first()->pivot->name ?? $item->name;
                                    $cCanonical = write_url($item->languages->first()->pivot->canonical ?? $item->canonical);
                                @endphp
                                <a href="{{ $cCanonical }}" class="tag-pill-link">
                                    {{ $cName }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Brands list (Khối 2) for Root Category -->
                @if(isset($brands) && $brands->isNotEmpty())
                    <div class="root-section-block root-brands-block" style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 25px;">
                        <h3 class="root-section-title">Thương hiệu nổi bật</h3>
                        <div class="brands-logos-grid">
                            @foreach($brands as $brand)
                                @php
                                    $brandLink = request()->fullUrlWithQuery(['brand' => $brand->canonical]);
                                @endphp
                                <a href="{{ $brandLink }}" class="brand-logo-card">
                                    <span class="brand-name-text">{{ $brand->name }}</span>
                                    <span class="brand-count-sub">{{ $brand->count }} sản phẩm</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Main Body: 2 Columns (3/4 Left & 1/4 Right) -->
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium" data-uk-grid-margin>
                
                <!-- Left 3/4 Column: Product Listing & Brand Filter -->
                <div class="uk-width-large-3-4 left-product-column">
                    
                    <!-- Brand Filters (Only for subcategories / child directories) -->
                    @if(!$isRootCategory && isset($brands) && $brands->isNotEmpty())
                        <div class="brand-filter-container">
                            <span class="brand-filter-label">Thương hiệu:</span>
                            <div class="brand-list-row">
                                <a href="{{ request()->fullUrlWithQuery(['brand' => null]) }}" class="brand-filter-item @if(!request('brand')) active @endif">
                                    Tất cả
                                </a>
                                @foreach($brands as $brand)
                                    <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->canonical]) }}" class="brand-filter-item @if(request('brand') === $brand->canonical || request('brand') == $brand->id) active @endif">
                                        {{ $brand->name }} <span class="brand-count-badge">({{ $brand->count }})</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Sorting & Count Header -->
                    <div class="prd-grid-header uk-flex uk-flex-middle uk-flex-space-between uk-margin-bottom">
                        <div class="results-count">
                            @if(request('keyword'))
                                Kết quả tìm kiếm cho "{{ request('keyword') }}": <span class="count-num">{{ $products->total() }}</span> sản phẩm
                            @else
                                Hiển thị <span class="count-num">{{ $products->total() }}</span> kết quả
                            @endif
                        </div>
                        <div class="prd-sort">
                            <select class="sort-select" name="sortType" id="sortType" onchange="if(this.value) { window.location.href = '{{ request()->fullUrlWithQuery(['sort' => '_placeholder_']) }}'.replace('_placeholder_', this.value); }">
                                <option value="">Sắp xếp</option>
                                <option value="price-asc" @if(request('sort') === 'price-asc') selected @endif>Giá tăng dần</option>
                                <option value="price-desc" @if(request('sort') === 'price-desc') selected @endif>Giá giảm dần</option>
                                <option value="name-asc" @if(request('sort') === 'name-asc') selected @endif>Tên A-Z</option>
                                <option value="name-desc" @if(request('sort') === 'name-desc') selected @endif>Tên Z-A</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="product-list">
                        @if (!is_null($products) && count($products))
                            <div class="products-grid-wrapper">
                                @foreach ($products as $keyPost => $valPost)
                                    @php
                                        $title = $valPost->name ?? '';
                                        $canonical = write_url($valPost->canonical);
                                        $image = image($valPost->image);
                                        $priceInfo = getProductPriceInfo($valPost);
                                    @endphp
                                    <div class="product-grid-item">
                                        <a href="{{ $canonical }}" class="product-link">
                                            <div class="product-image-box">
                                                <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
                                            </div>
                                            <div class="product-info-box">
                                                <h4 class="product-title">{{ $title }}</h4>
                                                
                                                <div class="product-price-row">
                                                    <span class="product-sale-price">{{ convert_price($priceInfo['priceSale'], true) }}₫</span>
                                                    @if($priceInfo['percent'] > 0)
                                                        <span class="product-discount-badge">Giảm {{ $priceInfo['percent'] }}%</span>
                                                    @endif
                                                </div>
                                                @if($priceInfo['percent'] > 0)
                                                    <div class="product-old-price-row">
                                                        <span class="product-old-price">{{ convert_price($priceInfo['price'], true) }}₫</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="uk-flex uk-flex-center uk-margin-large-top">
                                @include('frontend.component.pagination', ['model' => $products])
                            </div>
                        @else
                            <div class="no-products uk-text-center uk-margin-large-top uk-margin-large-bottom">
                                <p style="font-family: 'Manrope', sans-serif; font-size: 15px; color: #888;">Không tìm thấy sản phẩm nào phù hợp.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Category Content -->
                    @if(!empty($productCatalogue->content))
                        <div class="bottom-category-content uk-margin-large-top">
                            <h2 class="bottom-content-title">Thông tin danh mục</h2>
                            <div class="bottom-content-body">
                                {!! $productCatalogue->content !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right 1/4 Column: Sidebar -->
                <div class="uk-width-large-1-4 right-sidebar-column">
                    
                    <!-- Box 1: Bảo hành giá -->
                    <div class="sidebar-box warranty-box-container mb20">
                        <div class="warranty-box-header">
                            <img src="/userfiles/image/slide/logo.png" alt="" class="warranty-header-icon" onerror="this.style.display='none'">
                            <h4>BẢO HÀNH GIÁ</h4>
                        </div>
                        <div class="warranty-box-body">
                            <div class="warranty-seal-img-box">
                                <img src="/userfiles/image/slide/warranty_seal.png" alt="Bảo hành giá" onerror="this.src='/userfiles/image/slide/logo.png'">
                            </div>
                            <div class="warranty-desc-content" style="text-align: left; font-size: 13px; line-height: 1.6; color: #444;">
                                <p style="margin-top: 0; font-weight: bold; color: #d61c00; text-align: center;">CAM KẾT 4 TỐT:</p>
                                <ul style="padding-left: 15px; margin: 0; list-style-type: disc;">
                                    <li style="margin-bottom: 5px;"><b>Sản phẩm Tốt:</b> Nguồn gốc rõ ràng, chất lượng kiểm định kĩ càng.</li>
                                    <li style="margin-bottom: 5px;"><b>Dịch vụ Tốt:</b> Giao hàng nhanh chóng, tư vấn tận tâm.</li>
                                    <li style="margin-bottom: 5px;"><b>Bảo hành Tốt:</b> Lỗi 1 đổi 1 nhanh chóng, uy tín.</li>
                                    <li style="margin-bottom: 5px;"><b>Giá thành Tốt:</b> Cam kết mức giá cạnh tranh nhất.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Search Box inside this catalogue -->
                    <div class="sidebar-box search-box-container mb20">
                        <form action="" method="GET" class="sidebar-search-form">
                            <button type="submit" class="sidebar-search-submit"><i class="fa fa-search"></i></button>
                            <input type="text" name="keyword" placeholder="Tìm kiếm trong mục..." value="{{ request('keyword') }}" class="sidebar-search-input">
                            @if(request('brand'))
                                <input type="hidden" name="brand" value="{{ request('brand') }}">
                            @endif
                        </form>
                    </div>

                    <!-- Box 3: Filter Category Select box -->
                    <div class="sidebar-box filter-box-container mb20">
                        <h5 class="filter-box-title">Danh mục khác</h5>
                        <select class="sidebar-category-select-dropdown" onchange="if(this.value) window.location.href=this.value;">
                            <option value="">Chọn một danh mục</option>
                            @foreach($allCategories as $cat)
                                @php
                                    $catName = $cat->languages->first()->pivot->name ?? $cat->name;
                                    $catCanonical = write_url($cat->languages->first()->pivot->canonical ?? $cat->canonical);
                                @endphp
                                <option value="{{ $catCanonical }}" @if($cat->id == $productCatalogue->id) selected @endif>{{ $catName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Box 4: Giới thiệu Gom -->
                    <div class="sidebar-box intro-box-container">
                        <div class="intro-box-header">
                            <img src="/userfiles/image/slide/logo.png" alt="" class="intro-header-icon" onerror="this.style.display='none'">
                            <h4>Giới thiệu Gom</h4>
                        </div>
                        <div class="intro-box-body">
                            <div class="social-links-row mb15">
                                <a href="{{ $system['homepage_intro_youtube'] ?? '#' }}" target="_blank" class="social-btn youtube-btn">
                                    <i class="fa fa-youtube-play"></i>
                                    <span>YOUTUBE</span>
                                </a>
                                <a href="{{ $system['homepage_intro_tiktok'] ?? '#' }}" target="_blank" class="social-btn tiktok-btn">
                                    <img src="/userfiles/image/slide/logo.png" alt="TikTok" style="width: 14px; height: 14px; margin-right: 4px; display: inline-block; vertical-align: middle; filter: brightness(0) invert(1);" onerror="this.style.display='none'">
                                    <span>Gomhang.vn</span>
                                </a>
                            </div>
                            @if(!empty($system['homepage_intro_image']))
                                <div class="intro-banner-img-box">
                                    <img src="{{ $system['homepage_intro_image'] }}" alt="Giới thiệu Gom">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<style>
/* Base Manrope Font configuration */
#prd-catalogue {
    font-family: 'Manrope', sans-serif;
    background-color: #fbfbfb;
    padding-bottom: 60px;
}

/* Category Hero Section */
.cat-hero-section {
    background-color: #f3f3f3;
    padding: 35px 0;
    text-align: center;
    border-bottom: 1px solid #e7e7e7;
    margin-bottom: 25px;
}
.cat-hero-title {
    font-size: 26px;
    font-weight: bold;
    color: #222222;
    margin: 0 0 10px 0;
    text-transform: capitalize;
}
.cat-hero-breadcrumbs {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #666666;
}
.cat-hero-breadcrumbs a {
    color: #666666;
    text-decoration: none;
}
.cat-hero-breadcrumbs a:hover {
    color: #d61c00;
}
.cat-hero-breadcrumbs .separator {
    color: #999999;
}

/* Description box styling */
.cat-description-box {
    font-size: 14px;
    color: #555555;
    line-height: 1.6;
    max-width: 900px;
    margin: 0 auto 30px auto;
}

/* Root Category sub-blocks layout */
.root-category-sections {
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 6px;
    padding: 25px;
    margin-bottom: 35px;
}
.root-section-block {
    margin-bottom: 25px;
}
.root-section-block:last-child {
    margin-bottom: 0;
}
.root-section-title {
    font-size: 15px;
    font-weight: bold;
    color: #333333;
    font-style: italic;
    margin-top: 0;
    margin-bottom: 12px;
}
.root-tags-cloud {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 10px;
}
.tag-pill-link {
    background-color: #ffffff;
    border: 1px solid #dddddd;
    border-radius: 4px;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: bold;
    color: #333333;
    text-decoration: none !important;
    transition: all 0.2s;
}
.tag-pill-link:hover {
    border-color: #d61c00;
    color: #d61c00;
    background-color: #fff8f7;
}

/* Brand filter tag cloud */
.brand-filter-container {
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 6px;
    padding: 15px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}
.brand-filter-label {
    font-weight: bold;
    font-size: 14px;
    margin-right: 15px;
    color: #333;
}
.brand-list-row {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}
.brand-filter-item {
    font-size: 13px;
    padding: 5px 14px;
    border: 1px solid #dddddd;
    border-radius: 4px;
    text-decoration: none !important;
    color: #333333;
    font-weight: bold;
    background: #ffffff;
    transition: all 0.2s;
}
.brand-filter-item:hover,
.brand-filter-item.active {
    border-color: #d61c00 !important;
    color: #d61c00 !important;
    background-color: #fff8f7 !important;
}
.brand-count-badge {
    font-weight: normal;
    color: #777;
    margin-left: 2px;
}

/* Sorting & count header */
.prd-grid-header {
    border-bottom: 1px solid #eeeeee;
    padding-bottom: 12px;
    margin-bottom: 20px;
}
.results-count {
    font-size: 14px;
    font-weight: 500;
    color: #555555;
}
.results-count .count-num {
    font-weight: bold;
    color: #d61c00;
}
.sort-select {
    border: 1px solid #cccccc;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
    color: #333;
    outline: none;
    background: #ffffff;
}

/* Products Grid layout inside 3/4 left column */
.products-grid-wrapper {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

/* Sidebar Styling */
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
.warranty-box-header h4,
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
    background-color: #d61c00;
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

/* Sidebar Category Select */
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

/* Social links and intro */
.social-links-row {
    display: flex;
    gap: 10px;
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
    min-width: 100px;
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

/* Bottom description content box */
.bottom-category-content {
    background: #ffffff;
    border: 1px solid #eeeeee;
    border-radius: 6px;
    padding: 25px;
}
.bottom-content-title {
    font-size: 18px;
    font-weight: bold;
    color: #333333;
    margin-top: 0;
    margin-bottom: 15px;
}
.bottom-content-body {
    font-size: 14px;
    color: #444444;
    line-height: 1.6;
}
.bottom-content-body p {
    margin-top: 0;
    margin-bottom: 12px;
}
.mb20 {
    margin-bottom: 20px;
}
.mb15 {
    margin-bottom: 15px;
}

/* Brands grid for root category */
.brands-logos-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
}
.brand-logo-card {
    flex: 1 1 calc(16.666% - 15px);
    min-width: 120px;
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 15px 10px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-decoration: none !important;
    transition: all 0.3s ease;
}
.brand-logo-card:hover {
    border-color: #d61c00;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.brand-name-text {
    font-size: 15px;
    font-weight: 700;
    color: #333;
    margin-bottom: 2px;
}
.brand-count-sub {
    font-size: 11px;
    color: #888;
}
</style>
@endsection
