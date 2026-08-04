@extends('frontend.homepage.layout')
@section('content')

{{--
    Search results.

    This page used to render products with the old .product-item markup left over
    from the previous template: no price, no discount badge, and the image URL was
    used raw instead of going through image(). It now uses the same card as the
    catalogue and homepage so a product looks the same wherever it appears.

    Products and posts are separate tabs; only the active one is paginated.
--}}

<div id="search-page" class="page-body">

    <div class="cat-hero-section">
        <div class="uk-container uk-container-center cat-hero-container">
            <h1 class="cat-hero-title">
                @if($keyword !== '')
                    Kết quả tìm kiếm: “{{ $keyword }}”
                @else
                    Tìm kiếm sản phẩm
                @endif
            </h1>
            <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs">
                <li><a href="/">Trang chủ</a></li>
                <li class="separator">&raquo;</li>
                <li>Tìm kiếm</li>
            </ul>
        </div>
    </div>

    <div class="uk-container uk-container-center">

        {{-- Re-submit form so the keyword can be refined without going back to the header. --}}
        <form action="{{ url('tim-kiem') }}" method="GET" class="search-again-form">
            <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Nhập từ khóa cần tìm..."
                   class="search-again-input" aria-label="Từ khóa tìm kiếm">
            <input type="hidden" name="type" value="{{ $type }}">
            <button type="submit" class="search-again-btn">Tìm kiếm</button>
        </form>

        <div class="search-tabs">
            <a href="{{ url('tim-kiem') }}?keyword={{ urlencode($keyword) }}&type=product"
               class="search-tab @if($type === 'product') active @endif">
                Sản phẩm <span class="search-tab__count">{{ $productTotal }}</span>
            </a>
            <a href="{{ url('tim-kiem') }}?keyword={{ urlencode($keyword) }}&type=post"
               class="search-tab @if($type === 'post') active @endif">
                Bài viết <span class="search-tab__count">{{ $postTotal }}</span>
            </a>
        </div>

        @if($type === 'product')

            @if(!is_null($products) && count($products))
                <div class="products-grid-wrapper search-results-grid">
                    @foreach ($products as $valPost)
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
                                    <h2 class="product-title">{{ $title }}</h2>
                                    <div class="product-price-row">
                                        <span class="product-sale-price">{{ convert_price($priceInfo['priceSale'], true) }}đ</span>
                                        @if($priceInfo['percent'] > 0)
                                            <span class="product-discount-badge">Giảm {{ $priceInfo['percent'] }}%</span>
                                        @endif
                                    </div>
                                    @if($priceInfo['percent'] > 0)
                                        <div class="product-old-price-row">
                                            <span class="product-old-price">{{ convert_price($priceInfo['price'], true) }}đ</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="uk-flex uk-flex-center uk-margin-large-top">
                    {{ $products->appends(['keyword' => $keyword, 'type' => 'product'])->links() }}
                </div>
            @else
                <div class="search-empty">
                    <p>Không tìm thấy sản phẩm nào cho “{{ $keyword }}”.</p>
                    @if($postTotal > 0)
                        <p>
                            Có <strong>{{ $postTotal }}</strong> bài viết phù hợp —
                            <a href="{{ url('tim-kiem') }}?keyword={{ urlencode($keyword) }}&type=post">xem bài viết</a>.
                        </p>
                    @endif
                </div>
            @endif

        @else

            @if(!is_null($posts) && count($posts))
                <div class="search-posts-list">
                    @foreach ($posts as $post)
                        @php
                            $postTitle = $post->name ?? '';
                            $postHref = write_url($post->canonical);
                            $postImage = image($post->image);
                            $postExcerpt = cutnchar(strip_tags((string) $post->description), 160);
                        @endphp
                        <article class="search-post-item">
                            <a href="{{ $postHref }}" class="search-post-item__image">
                                <img src="{{ $postImage }}" alt="{{ $postTitle }}" loading="lazy">
                            </a>
                            <div class="search-post-item__body">
                                <h2 class="search-post-item__title">
                                    <a href="{{ $postHref }}">{{ $postTitle }}</a>
                                </h2>
                                @if(!empty($post->created_at))
                                    <time class="search-post-item__date" datetime="{{ \Carbon\Carbon::parse($post->created_at)->toDateString() }}">
                                        {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y') }}
                                    </time>
                                @endif
                                @if($postExcerpt !== '')
                                    <p class="search-post-item__excerpt">{!! $postExcerpt !!}</p>
                                @endif
                                <a href="{{ $postHref }}" class="search-post-item__more">Đọc tiếp &rarr;</a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="uk-flex uk-flex-center uk-margin-large-top">
                    {{ $posts->appends(['keyword' => $keyword, 'type' => 'post'])->links() }}
                </div>
            @else
                <div class="search-empty">
                    <p>Không tìm thấy bài viết nào cho “{{ $keyword }}”.</p>
                    @if($productTotal > 0)
                        <p>
                            Có <strong>{{ $productTotal }}</strong> sản phẩm phù hợp —
                            <a href="{{ url('tim-kiem') }}?keyword={{ urlencode($keyword) }}&type=product">xem sản phẩm</a>.
                        </p>
                    @endif
                </div>
            @endif

        @endif
    </div>
</div>

<style>
#search-page {
    font-family: 'Manrope', sans-serif;
    background-color: #fbfbfb;
    padding-bottom: 60px;
}

/* Refine-search form */
.search-again-form {
    display: flex;
    gap: 10px;
    max-width: 560px;
    margin: 24px auto 18px;
}
.search-again-input {
    flex: 1 1 auto;
    min-width: 0;
    padding: 11px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}
.search-again-input:focus {
    border-color: #e02b1d;
    box-shadow: 0 0 0 3px rgba(224, 43, 29, .15);
    outline: none;
}
.search-again-btn {
    flex: 0 0 auto;
    padding: 11px 22px;
    border: none;
    border-radius: 6px;
    background: #e02b1d;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
}
.search-again-btn:hover { background: #b3210f; }

/* Product / post tabs */
.search-tabs {
    display: flex;
    gap: 4px;
    justify-content: center;
    border-bottom: 1px solid #e6e6e6;
    margin-bottom: 24px;
}
.search-tab {
    padding: 10px 20px;
    border-radius: 6px 6px 0 0;
    color: #555 !important;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none !important;
}
.search-tab:hover { color: #e02b1d !important; }
.search-tab.active {
    background: #e02b1d;
    color: #fff !important;
}
.search-tab__count {
    display: inline-block;
    min-width: 20px;
    margin-left: 4px;
    padding: 0 5px;
    border-radius: 10px;
    background: rgba(0, 0, 0, .08);
    font-size: 12px;
    font-weight: 700;
    text-align: center;
}
.search-tab.active .search-tab__count { background: rgba(255, 255, 255, .25); }

/* Product results reuse the catalogue card, so only the grid needs declaring. */
.search-results-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
}
@media (max-width: 959px) {
    .search-results-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 479px) {
    .search-results-grid { grid-template-columns: minmax(0, 1fr); }
}

.product-grid-item {
    background: #fff;
    border: 1px solid #eaeaea;
    border-radius: 4px;
    overflow: hidden;
    transition: box-shadow .2s ease, border-color .2s ease;
}
.product-grid-item:hover {
    border-color: #d61c00;
    box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
}
.product-grid-item .product-link { display: block; text-decoration: none !important; }
.product-grid-item .product-image-box {
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px;
}
.product-grid-item .product-image-box img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}
.product-grid-item .product-info-box { padding: 0 12px 14px; }
.product-grid-item .product-title {
    margin: 0 0 8px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    color: #222;
    /* Two lines so cards in a row stay the same height. */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.product-grid-item .product-price-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.product-grid-item .product-sale-price {
    color: #e02b1d;
    font-size: 16px;
    font-weight: 700;
}
.product-grid-item .product-discount-badge {
    padding: 1px 6px;
    border: 1px solid #cfcfcf;
    border-radius: 3px;
    color: #333;
    font-size: 11px;
    font-weight: 600;
}
.product-grid-item .product-old-price {
    color: #999;
    font-size: 12px;
    text-decoration: line-through;
}

/* Post results */
.search-posts-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.search-post-item {
    display: flex;
    gap: 16px;
    padding: 16px;
    background: #fff;
    border: 1px solid #eaeaea;
    border-radius: 6px;
}
.search-post-item__image {
    flex: 0 0 180px;
    max-width: 180px;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    border-radius: 4px;
}
.search-post-item__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.search-post-item__body { flex: 1 1 auto; min-width: 0; }
.search-post-item__title {
    margin: 0 0 6px;
    font-size: 17px;
    font-weight: 700;
    line-height: 1.35;
}
.search-post-item__title a { color: #1a1a1a !important; text-decoration: none !important; }
.search-post-item__title a:hover { color: #e02b1d !important; }
.search-post-item__date {
    display: block;
    margin-bottom: 8px;
    color: #999;
    font-size: 12.5px;
}
.search-post-item__excerpt {
    margin: 0 0 10px;
    color: #555;
    font-size: 14px;
    line-height: 1.65;
}
.search-post-item__more {
    color: #e02b1d !important;
    font-size: 13.5px;
    font-weight: 700;
    text-decoration: none !important;
}
@media (max-width: 575px) {
    .search-post-item { flex-direction: column; }
    .search-post-item__image { flex-basis: auto; max-width: 100%; }
}

.search-empty {
    padding: 40px 20px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 6px;
    color: #666;
    font-size: 15px;
    text-align: center;
}
.search-empty p { margin: 0 0 8px; }
.search-empty p:last-child { margin-bottom: 0; }
.search-empty a { color: #e02b1d; font-weight: 700; }
</style>

@endsection
