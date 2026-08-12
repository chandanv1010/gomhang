<!DOCTYPE html>
{{-- The site is Vietnamese; it declared lang="en", which misleads screen readers,
     translation prompts and search engines. --}}
<html lang="vi">
    <head>
        {{-- Ô "Script Head" trong Cấu hình -> Cấu hình script. Dùng cho mã theo dõi
             (Google Analytics, Tag Manager, Pixel) hoặc thẻ xác minh phát sinh sau.
             Phải là {!! !!} chứ không phải {{ }}: escape thì thẻ <script> in ra
             thành chữ chứ không chạy. --}}
        @if(trim((string) ($system['script_1'] ?? '')) !== '')
            {!! $system['script_1'] !!}
        @endif
        @include('frontend.component.head')
        @vite('resources/css/app.scss')
    </head>
    <body>
        {{-- Schema markup belongs inside the document, not in the gap between
             </head> and <body> where it used to sit - that is invalid placement
             and browsers relocate it. --}}
        @if(isset($schema))
            {!! $schema !!}
        @endif
        @include('frontend.component.header')
        @yield('content')
        @include('frontend.component.footer')
        @include('frontend.component.floating-contact')
        @include('frontend.component.script')
        @vite('resources/js/app.js')
        @if(trim((string) ($system['script_2'] ?? '')) !== '')
            {!! $system['script_2'] !!}
        @endif
    </body>
</html>