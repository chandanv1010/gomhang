(function ($) {
    "use strict";
    var HT = {};
    HT.carousel = () => {
        if ($(".home-slider").length > 0) {
            $(".home-slider").owlCarousel({
                loop: true,
                nav: false,
                autoplay: true,
                responsive: {
                    0: {
                        nav: false,
                        items: 1,
                    },
                    600: {
                        items: 1,
                    },
                    1000: {
                        nav: false,
                        items: 1,
                    },
                },
            });
        }
    };
    HT.productSlide = () => {
        if ($(".product-carousel").length > 0) {
            $(".product-carousel").owlCarousel({
                loop: true,
                nav: false,
                autoplay: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,

                    },
                    600: {
                        items: 2,
                    },
                    1000: {
                        items: 3,
                    },
                },
            });
        }
        $('.owl-next-custom').click(function () {
            $(".product-carousel").trigger('next.owl.carousel');
        });
        
        $('.owl-prev-custom').click(function () {
            $(".product-carousel").trigger('prev.owl.carousel');
        });
        
    }
    HT.cateProductCarousel = () => {
        if ($(".cate-product-carousel").length > 0) {
            console.log(2);
            $(".cate-product-carousel").owlCarousel({
                loop: true,
                nav: false,
                autoplay: true,
                dots: false,
                responsive: {
                    0: {
                        nav: false,
                        items: 1,

                    },
                    600: {
                        items: 2,
                    },
                    1000: {
                        nav: false,
                        items: 3,
                    },
                },
            });
        }
    };

    HT.certificates = () => {
        $(document).ready(function(){
            $('.certificates-carousel').owlCarousel({
                loop: true,
                margin: 30,
                nav: false,
                dots: false,
                center: true,
                autoplay: true,
                autoplayTimeout: 5000,
                autoplayHoverPause: true,
                smartSpeed: 1000,
                responsive: {
                    0: {
                        nav: false,
                        items: 1,

                    },
                    600: {
                        items: 1,
                        margin: 10
                    },

                    1200: {
                        nav: false,
                        items: 3,
                    },
                },
                onInitialized: function(event) {
                    // Thêm class center cho item đầu tiên
                    setTimeout(function() {
                        $('.owl-item.active').eq(Math.floor($('.owl-item.active').length / 2)).addClass('center');
                    }, 100);
                },
                onTranslated: function(event) {
                    // Xóa class center cũ và thêm cho item mới
                    $('.owl-item').removeClass('center');
                    $('.owl-item.active').eq(Math.floor($('.owl-item.active').length / 2)).addClass('center');
                }
            });
            
            // Tạo hiệu ứng hover cho certificates
            $('.certificate-item').hover(
                function() {
                    $(this).find('.certificate-image').css('transform', 'translateY(-10px) scale(1.02)');
                },
                function() {
                    $(this).find('.certificate-image').css('transform', 'translateY(0) scale(1)');
                }
            );
        });
    }

    HT.productSlideDetail = () => {
        const $container = $('.thumbnail-section');
    const $scrollContainer = $container.find('.vertical-carousel');
    const isMobile = window.innerWidth <= 768;

    if ($scrollContainer.length) {
        $scrollContainer.css({
            'scroll-behavior': 'smooth'
        });

        // Click thumbnail
        $(document).off('click', '.thumbnail-item').on('click', '.thumbnail-item', function (e) {
            e.preventDefault();
            $('.thumbnail-item').removeClass('active');
            $(this).addClass('active');

            const imageUrl = $(this).data('image');
            const $mainImage = $('#mainImage');

            if (imageUrl && $mainImage.length) {
                $mainImage.fadeOut(200, function () {
                    $mainImage.attr('src', imageUrl).fadeIn(200);
                });
                scrollToThumbnail($(this));
            }
        });

        // Scroll vào giữa
        function scrollToThumbnail($thumbnail) {
            if (isMobile) {
                const containerWidth = $scrollContainer.width();
                const thumbnailLeft = $thumbnail.position().left;
                const thumbnailWidth = $thumbnail.outerWidth();
                const scrollLeft = $scrollContainer.scrollLeft();

                const targetScrollLeft = scrollLeft + thumbnailLeft - (containerWidth / 2) + (thumbnailWidth / 2);
                $scrollContainer.animate({ scrollLeft: targetScrollLeft }, 300);
            } else {
                const containerHeight = $scrollContainer.height();
                const thumbnailTop = $thumbnail.position().top;
                const thumbnailHeight = $thumbnail.outerHeight();
                const scrollTop = $scrollContainer.scrollTop();

                const targetScrollTop = scrollTop + thumbnailTop - (containerHeight / 2) + (thumbnailHeight / 2);
                $scrollContainer.animate({ scrollTop: targetScrollTop }, 300);
            }
        }

        // Mouse wheel & touch scroll cho desktop
        if (!isMobile) {
            $scrollContainer.off('wheel').on('wheel', function (e) {
                e.preventDefault();
                const scrollAmount = 100;
                const currentScrollTop = $scrollContainer.scrollTop();

                if (e.originalEvent.deltaY > 0) {
                    $scrollContainer.animate({ scrollTop: currentScrollTop + scrollAmount }, 200);
                } else {
                    $scrollContainer.animate({ scrollTop: currentScrollTop - scrollAmount }, 200);
                }
            });

            let startY = 0;
            let isScrolling = false;

            $scrollContainer.off('touchstart').on('touchstart', function (e) {
                startY = e.originalEvent.touches[0].clientY;
                isScrolling = false;
            });

            $scrollContainer.off('touchmove').on('touchmove', function (e) {
                if (!isScrolling) {
                    const currentY = e.originalEvent.touches[0].clientY;
                    const diff = startY - currentY;

                    if (Math.abs(diff) > 10) {
                        isScrolling = true;
                        const scrollAmount = diff * 2;
                        const currentScrollTop = $scrollContainer.scrollTop();
                        $scrollContainer.scrollTop(currentScrollTop + scrollAmount);
                    }
                }
            });
        }

        // Keyboard điều hướng
        $(document).off('keydown.thumbnail').on('keydown.thumbnail', function (e) {
            const $thumbnails = $('.thumbnail-item');
            const $active = $thumbnails.filter('.active');
            let $next;

            if (e.keyCode === 38) {
                e.preventDefault();
                $next = $active.parent().prev().find('.thumbnail-item');
                if (!$next.length) $next = $thumbnails.last();
            } else if (e.keyCode === 40) {
                e.preventDefault();
                $next = $active.parent().next().find('.thumbnail-item');
                if (!$next.length) $next = $thumbnails.first();
            }

            if ($next && $next.length) $next.trigger('click');
        });

        // Auto active đầu tiên
        setTimeout(() => {
            const $first = $('.thumbnail-item').first();
            $first.addClass('active').trigger('click');
            scrollToThumbnail($first);
        }, 1000);
    }    
    }
    
    $(document).ready(function () {
        HT.productSlide();
        HT.carousel();
        HT.cateProductCarousel();
        HT.certificates();
        HT.productSlideDetail();
    });
})(jQuery);
