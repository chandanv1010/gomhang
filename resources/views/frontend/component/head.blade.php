@php
    // SEO values with fallbacks, so no page ships an empty title or description.
    $brandName    = system_brand($system ?? null);
    $siteUrl      = rtrim((string) config('app.url'), '/');

    $metaTitle    = trim((string) ($seo['meta_title'] ?? '')) ?: $brandName;
    $metaDesc     = trim((string) ($seo['meta_description'] ?? ''));
    $metaKeywords = trim((string) ($seo['meta_keyword'] ?? ''));
    $canonical    = \App\Support\Schema::absolute($seo['canonical'] ?? request()->url());

    // og:image has to be absolute; fall back to the site logo so a share is never blank.
    $metaImage    = \App\Support\Schema::absolute(
        ($seo['meta_image'] ?? '') ?: ($system['homepage_logo'] ?? '')
    );

    // Per-page robots directive; search and other thin pages pass 'noindex,follow'.
    $robots       = trim((string) ($seo['follow'] ?? '')) ?: 'index,follow';

    // 'product' on a product page, 'article' on a post, otherwise 'website'.
    $ogType       = trim((string) ($seo['og_type'] ?? '')) ?: 'website';

    // Mã xác minh chủ sở hữu (Google Search Console / Bing). Lấy từ Cấu hình SEO
    // trong admin nên đổi được mà không phải sửa code. Google chấp nhận cả thẻ
    // meta lẫn tệp HTML ở gốc site; tệp nằm ở public/, thẻ này là cách thứ hai.
    // Nếu người dùng dán nguyên cả thẻ <meta ...> thì bóc lấy phần content.
    $xacMinhGoogle = trim((string) ($system['seo_google_verification'] ?? ''));
    $xacMinhBing   = trim((string) ($system['seo_bing_verification'] ?? ''));
    $bocMaXacMinh = function (string $gt): string {
        if (stripos($gt, '<meta') !== false && preg_match('/content=["\']([^"\']+)["\']/i', $gt, $khop)) {
            return trim($khop[1]);
        }
        return $gt;
    };
    $xacMinhGoogle = $bocMaXacMinh($xacMinhGoogle);
    $xacMinhBing   = $bocMaXacMinh($xacMinhBing);
@endphp
<base href="{{ $siteUrl }}/" />
<meta charset="utf-8" />
{{-- maximum-scale=1 / user-scalable=0 blocked pinch zoom, which fails WCAG 1.4.4. --}}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Removed: <meta http-equiv="refresh" content="1800">. It reloaded every page
     every 30 minutes, discarding anything a visitor had typed or scrolled to, and
     fails WCAG 2.2.1. --}}

@if($xacMinhGoogle !== '')
    <meta name="google-site-verification" content="{{ $xacMinhGoogle }}" />
@endif
@if($xacMinhBing !== '')
    <meta name="msvalidate.01" content="{{ $xacMinhBing }}" />
@endif

<meta name="robots" content="{{ $robots }}" />
<meta name="author" content="{{ $brandName }}" />
<meta name="copyright" content="{{ $brandName }}" />
@if(!empty($system['homepage_favicon']))
    <link rel="icon" href="{{ $system['homepage_favicon'] }}" type="image/png" sizes="30x30">
@endif

<title>{{ $metaTitle }}</title>
@if($metaDesc !== '')
    <meta name="description" content="{{ $metaDesc }}" />
@endif
@if($metaKeywords !== '')
    {{-- The correct attribute is "keywords"; the old markup wrote "keyword". --}}
    <meta name="keywords" content="{{ $metaKeywords }}" />
@endif
<link rel="canonical" href="{{ $canonical }}" />

{{-- Open Graph --}}
<meta property="og:locale" content="vi_VN" />
<meta property="og:site_name" content="{{ $brandName }}" />
<meta property="og:type" content="{{ $ogType }}" />
<meta property="og:title" content="{{ $metaTitle }}" />
<meta property="og:url" content="{{ $canonical }}" />
@if($metaDesc !== '')
    <meta property="og:description" content="{{ $metaDesc }}" />
@endif
@if($metaImage !== '')
    <meta property="og:image" content="{{ $metaImage }}" />
    <meta property="og:image:alt" content="{{ $metaTitle }}" />
@endif

{{-- Twitter. Empty fb:admins / fb:app_id tags were dropped: a tag with no value
     is worse than no tag. --}}
<meta name="twitter:card" content="{{ $metaImage !== '' ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $metaTitle }}" />
@if($metaDesc !== '')
    <meta name="twitter:description" content="{{ $metaDesc }}" />
@endif
@if($metaImage !== '')
    <meta name="twitter:image" content="{{ $metaImage }}" />
@endif

{{-- preconnect so the font handshake starts before the CSS is parsed --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('frontend/resources/plugins/wow/css/libs/animate.css') }}">

<script src="{{ asset('vendor/frontend/library/js/jquery.js') }}"></script>
