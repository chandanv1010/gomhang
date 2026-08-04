<!DOCTYPE html>
{{-- The site is Vietnamese; it declared lang="en", which misleads screen readers,
     translation prompts and search engines. --}}
<html lang="vi">
    <head>
        {{-- {{ $system['script_1'] }} --}}
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
        {{-- {{ $system['script_2'] }} --}}
    </body>
</html>