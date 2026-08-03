{{-- jQuery core --}}


{{-- Toastr --}}
<script src="{{ asset('vendor/backend/js/plugins/toastr/toastr.min.js') }}"></script>

{{-- Nice Select --}}
{{-- <script src="{{ asset('vendor/frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js') }}"></script> --}}

{{-- UIkit --}}
<script src="{{ asset('vendor/frontend/uikit/js/uikit.min.js') }}"></script>
<script src="{{ asset('vendor/frontend/uikit/js/components/sticky.min.js') }}"></script>
<script src="{{ asset('vendor/frontend/uikit/js/components/accordion.min.js') }}"></script>
<script src="{{ asset('vendor/frontend/uikit/js/components/lightbox.min.js') }}"></script>
<script src="{{ asset('frontend/core/library/cart.js') }}"></script>

{{-- Swiper từ CDN (hoặc copy về public/vendor/frontend/resources/swiper/js/) --}}
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


<script src="{{ asset('frontend/resources/plugins/wow/dist/wow.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth >= 960) {
            new WOW().init();
        } else {
            // Remove wow class and make visible on mobile to avoid elements staying hidden
            var wowElements = document.querySelectorAll('.wow');
            wowElements.forEach(function(el) {
                el.classList.remove('wow');
                el.style.visibility = 'visible';
                el.style.animationName = 'none';
            });
        }
    });
</script>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0&appId=103609027035330&autoLogAppEvents=1" nonce="E1aWx0Pa"></script>
