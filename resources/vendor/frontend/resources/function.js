
(function($) {
    
    "use strict";
    var HT = {}; // Khai báo là 1 đối tượng
    var timer;
    var $carousel = $(".owl-slide");
    var _token = $('meta[name="csrf-token"]').attr('content');

    HT.swiperOption = (setting) => {
        // console.log(setting);
        let option = {}
        if(setting.animation.length){
            option.effect = setting.animation;
        }	
        if(setting.arrow === 'accept'){
            option.navigation = {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
        }
        if(setting.autoplay === 'accept'){
            option.autoplay = {
                delay: 50000,
                disableOnInteraction: false,
            }
        }
        if(setting.navigate === 'dots'){
            option.pagination = {
                el: '.swiper-pagination',
            }
        }
        return option
    }

    /* MAIN VARIABLE */
    HT.swiper = () => {
        var swiper = new Swiper(".panel-slide .swiper-container", {
            loop: false,
            pagination: {
                el: '.swiper-pagination',
            },
            autoplay: {
                delay : 5000,
            },
            spaceBetween: 15,
            slidesPerView: 1.5,
            breakpoints: {
                100: {
                    slidesPerView: 1,
                },
                500: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 1,
                },
                1280: {
                    slidesPerView: 1,
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
        });
    }

    HT.major = () => {
        var swiper = new Swiper(".panel-web .swiper-container", {
            loop: false,
            pagination: {
                el: '.swiper-pagination',
            },
            autoplay: {
                delay : 2000,
            },
            spaceBetween: 15,
            slidesPerView: 1.5,
            breakpoints: {
                415: {
                    slidesPerView: 1,
                },
                500: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1280: {
                    slidesPerView: 2.5,
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
        });
    }

    HT.category = () => {
        var swiper = new Swiper(".panel-category .swiper-container", {
            loop: false,
            pagination: {
                el: '.swiper-pagination',
            },
            autoplay: {
                delay : 2000,
            },
            spaceBetween: 15,
            slidesPerView: 1.5,
            breakpoints: {
                415: {
                    slidesPerView: 1,
                },
                500: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1280: {
                    slidesPerView: 2,
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
        });
    }



    HT.niceSelect = () => {
        if($('.nice-select').length){
            $('.nice-select').niceSelect();
        }
        
    }

    HT.select2 = () => {
        if($('.setupSelect2').length){
            $('.setupSelect2').select2();
        }
        
    }


    HT.skeleton = () => {
        
        document.addEventListener("DOMContentLoaded", function() {
            // Lựa chọn tất cả các ảnh cần lazy load
            const lazyImages = document.querySelectorAll('.lazy-image');
            
            // Tạo Intersection Observer
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    // Khi phần tử trở nên visible
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        // Lấy nguồn ảnh từ thuộc tính data-src
                        const src = img.dataset.src;
                        
                        // Tạo ảnh mới và thiết lập trình xử lý sự kiện onload
                        const newImg = new Image();
                        newImg.onload = function() {
                            // Khi ảnh đã tải xong, gán src và thêm class loaded
                            img.src = src;
                            img.classList.add('loaded');
                            
                            // Ẩn skeleton loading
                            const parent = img.closest('.image');
                            if (parent) {
                                const skeleton = parent.querySelector('.skeleton-loading');
                                if (skeleton) {
                                    skeleton.style.display = 'none';
                                }
                            }
                            
                            // Ngừng quan sát phần tử này
                            observer.unobserve(img);
                        };
                        
                        // Bắt đầu tải ảnh
                        newImg.src = src;
                    }
                });
            }, {
                // Tùy chọn: thiết lập ngưỡng và root
                rootMargin: '0px 0px 50px 0px', // Tải trước ảnh khi chúng cách 50px từ viewport
                threshold: 0.1 // Kích hoạt khi ít nhất 10% của ảnh trở nên visible
            });
            
            // Quan sát mỗi ảnh
            lazyImages.forEach(img => {
                observer.observe(img);
            });
        });
    }


    HT.removePagination = () => {
        $('.filter-content').on('slide', function() {
            $('.uk-flex .pagination').hide();
        });
    };


    HT.wrapTable = () => {
        var width = $(window).width()
        if(width < 600){
            $('table').wrap('<div class="uk-overflow-container"></div>')
        }
    }

    HT.addVoucher = () => {
        $(document).on('click','.info-voucher', function(e){
            e.preventDefault()
            let _this = $(this)
            _this.toggleClass('active');
        })
    }

    HT.advise = () => {
        $(document).on('click','.suggest-aj button', function(e){
            e.preventDefault()
            let _this = $(this)
            let option  = {
                name : $('#suggest input[name=name]').val(),
                gender : $('#suggest input[name=gender]').val(),
                phone : $('#suggest input[name=phone]').val(),
                address: $('#suggest input[name=address]').val(),
                post_id : $('#suggest input[name=post_id ]').val(),
                product_id : $('#suggest input[name=product_id ]').val(),
                _token: _token,
            }
            toastr.success('Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ vs bạn !', 'Thông báo từ hệ thống')
            $.ajax({
                url: 'ajax/contact/advise', 
                type: 'POST', 
                data: option, 
                dataType: 'json', 
                beforeSend: function() {
                    
                },
                success: function(res) {
                    console.log(res)
                    if(res.code === 10){
                        
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    }else if(res.status === 422){
                        let errors = res.messages;
                        for(let field in errors){
                            let errorMessage = errors[field];
                            $('.'+ field + '-error').text(errorMessage);
                        }
                    }
                },
            });
            
        })
    }

    HT.scroll = () => {
        $(document).ready(function() {
            $('a[href="#panel-contact"]').on('click', function(event) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: $('#panel-contact').offset().top - 50
                }, 800); 
            });
        });
    }



    HT.scrollHeading = () => {
        $(document).on('click', '.widget-toc a', function(e) {
            e.preventDefault(); // Ngăn hành vi mặc định của thẻ a
            
            let _this = $(this);
            let href = _this.attr('href'); // Lấy giá trị href
            
            // Kiểm tra nếu href bắt đầu bằng #
            if (href && href.startsWith('#')) {
                let targetId = href.substring(1); // Loại bỏ dấu # đầu tiên
                
                // Sử dụng document.getElementById thay vì jQuery selector để tránh lỗi
                let targetElement = document.getElementById(targetId);
                
                // Kiểm tra xem element có tồn tại không
                if (targetElement) {
                    // Chuyển về jQuery object để sử dụng offset()
                    let $targetElement = $(targetElement);
                    
                    // Cuộn mượt đến element
                    $('html, body').animate({
                        scrollTop: $targetElement.offset().top - 100 // Trừ 100px để tạo khoảng cách
                    }, 800); // 800ms cho hiệu ứng cuộn mượt
                    
                    // Thêm class active cho liên kết được click
                    $('.widget-toc a').removeClass('active');
                    _this.addClass('active');
                } else {
                    console.log('Không tìm thấy element với ID:', targetId);
                }
            }
        });
    }


    HT.highlightTocOnScroll = () => {
        $(window).on('scroll', function() {
            let scrollTop = $(window).scrollTop();
            
            $('.widget-toc a').each(function() {
                let href = $(this).attr('href');
                if (href && href.startsWith('#')) {
                    let targetId = href.substring(1);
                    let targetElement = document.getElementById(targetId); // Sử dụng getElementById
                    
                    if (targetElement) {
                        let $targetElement = $(targetElement);
                        let elementTop = $targetElement.offset().top - 150;
                        let elementBottom = elementTop + $targetElement.outerHeight();
                        
                        if (scrollTop >= elementTop && scrollTop < elementBottom) {
                            $('.widget-toc a').removeClass('active');
                            $(this).addClass('active');
                        }
                    }
                }
            });
        });
    }



    HT.popupSwiperSlide = () => {
        document.querySelectorAll(".popup-gallery").forEach(popup => {
            var swiper = new Swiper(popup.querySelector(".swiper-container"), {
                loop: true,
                // autoplay: {
                // 	delay: 2000,
                // 	disableOnInteraction: false,
                // },
                pagination: {
                    el: '.swiper-pagination',
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: {
                        el: popup.querySelector('.swiper-container-thumbs'),
                        slidesPerView: 4,
                        spaceBetween: 10,
                        slideToClickedSlide: true,
                    }
                }
            });
        });
    }


    HT.product = () => {
        var swiper = new Swiper(".panel-product .swiper-container", {
            loop: false,
            pagination: {
                el: '.swiper-pagination',
            },
            spaceBetween: 30,
            slidesPerView: 2,
            breakpoints: {
                315: {
                    slidesPerView: 1,
                },
                500: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1280: {
                    slidesPerView: 2,
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
        });
    }

    HT.feedback = () => {
        var swiper = new Swiper(".panel-feedback .swiper-container", {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
            },
            spaceBetween: 30,
            slidesPerView: 2,
            breakpoints: {
                315: {
                    slidesPerView: 1,
                },
                500: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1280: {
                    slidesPerView: 3,
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
        });
    }



    HT.partner = () => {
        var swiper = new Swiper(".panel-partner .swiper-container", {
            loop: false,
            pagination: {
                el: '.swiper-pagination',
            },
            spaceBetween: 30,
            slidesPerView: 2,
            breakpoints: {
                315: {
                    slidesPerView: 1,
                },
                500: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1280: {
                    slidesPerView: 6,
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            
        });
    }



    HT.register = () => {
        $('.register-form').on('submit', function(e){
            e.preventDefault()
            let _this = $(this)
            let option = {
                'email' : $('#reg_email').val(),
                'name' : $('#reg_name').val(),
                'phone' : $('#reg_phone').val(),
                'message' : $('#reg_message').val() + "<br>" + `Khóa học quan tâm: ${$('#reg_product_name').val()}`,
                '_token' : _token
            }

            $.ajax({
                url: 'ajax/contact/saveContact', 
                type: 'POST', 
                data: option,
                dataType: 'json', 
                beforeSend: function() {
                    // console.log(1234);
                    _this.find('.register-btn').html('Đang gửi dữ liệu...').attr('disabled', true)
                    // return false
                    
                },
                success: function(res) {
                    let inputValue = ((option.value == 1)?2:1)
                    if(res.flag == true){
                        _this.val(inputValue)
                    }
                    _this.find('.register-btn').html('Đăng ký ngay').removeAttr('disabled')
                    alert('Gửi thông tin liên hệ thành công. Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất')
                    _this[0].reset()
                    
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    
                    console.log('Lỗi: ' + textStatus + ' ' + errorThrown);
                }
            });
            
        })
    }

    HT.previewVideo = () => {
        $('.preview-video').on('click', function(e){
            e.preventDefault()
            let _this = $(this)
            let video = JSON.parse(_this.attr('data-video'))
            
            // Parse iframe và thêm autoplay
            let $iframe = $(video)
            let src = $iframe.attr('src')
            
            if (src) {
                // Thêm autoplay parameter
                let separator = src.includes('?') ? '&' : '?'
                let newSrc = src + separator + 'autoplay=1'
                
                // Có thể thêm thêm parameters khác
                newSrc += '&mute=1' // Mute để tránh browser block autoplay
                
                $iframe.attr('src', newSrc)
            }
            
            $('.video-feature').html($iframe[0].outerHTML)
        })
    }



    HT.changeStatusChildren = () => {
        $(document).on('click', '.toggle', function () {
            let $item = $(this).closest('.filter-list__item'); 
            let $children = $item.find('.children').first(); 
            if ($children.hasClass('active')) {
                $(this).removeClass('rotate');
                $children.removeClass('active');
            } else {
                $(this).addClass('rotate');
                $children.addClass('active');
            }
        });
    }

    HT.changeStatusPass = () => {
        $(document).on('click', '.password-toggle', function(e) {
            e.preventDefault();
            const $passwordInput = $(this).siblings('input[type="password"], input[type="text"]');
            const currentType = $passwordInput.attr('type');
            const inputId = $passwordInput.attr('id');
            if (currentType === 'password') {
                $passwordInput.attr('type', 'text');
                $(`#eye-${inputId}`).hide();
                $(`#eye-slash-${inputId}`).show();
            } else {
                $passwordInput.attr('type', 'password');
                $(`#eye-${inputId}`).show();
                $(`#eye-slash-${inputId}`).hide();
            }
        });
    }

    HT.changeStatusDropdownMenu = () => {
        $(document).on('click', '.browse-tools .dropdown', function() {
            let _this = $(this)
            _this.toggleClass('active')
            if(_this.hasClass('active')){
                _this.closest('.browse-tools').find('.dropdown-menu').addClass('open')
            }else{
                _this.closest('.browse-tools').find('.dropdown-menu').removeClass('open')
            }
        });
    }

    HT.collapse = () => {{
        $(document).on('click', '[data-bs-toggle="collapse"]', function() {
            let target = $($(this).data('bs-target'));
            target.hasClass('show') ? target.removeClass('show') : target.addClass('show');
        });
    }}


    HT.buynow = () => {
        $('#form-order-buy').on('submit', function(e){
            e.preventDefault();
            e.stopPropagation();

            let form = $(this);
            let action = "/ajax/order/buy/now"; // URL xử lý
            let data = form.serialize();

            $('.modal-content').addClass('loading');      // bật loader
            $('.error').addClass('hidden');

            $.ajax({
                url: action,
                type: "POST",
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    $('.modal-content').removeClass('loading');

                    if(res.code === "error"){
                        $('.error').removeClass('hidden');
                        $('.error .alert').html(res.message);
                        return;
                    }

                    if(res.code === "success"){
                        UIkit.modal('#order-buy').hide(); // đóng modal
                        alert("Gửi thông tin thành công! Chúng tôi sẽ liên hệ lại sớm nhất.");
                        form.trigger("reset"); // reset form
                    }
                },
                error: function(xhr){
                    $('.modal-content').removeClass('loading');
                    $('.error').removeClass('hidden');
                    $('.error .alert').html("Có lỗi xảy ra. Vui lòng thử lại!");
                }
            });
        });
    }

    HT.uploadAvatar = () => {

        // click nút -> mở chọn file
        $('#btnChangeAvatar').on('click', function () {
            $('#avatarInput').click();
        });

        // khi chọn file
        $('#avatarInput').on('change', function (e) {

            let file = this.files[0];
            if (!file) return;

            let formData = new FormData();
            formData.append('avatar', file);

            $('.avatar-wrapper').addClass('loading'); // bật loading
            $('.avatar-error').addClass('hidden');

            $.ajax({
                url: "/ajax/customer/uploadAvatar",
                type: "POST",
                data: formData,
                contentType: false, // quan trọng
                processData: false, // quan trọng
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('.avatar-wrapper').removeClass('loading');

                    if (res.status === "error") {
                        $('.avatar-error')
                            .removeClass('hidden')
                            .html(res.message);
                        return;
                    }

                    if (res.status === "success") {
                        $('#avatarPreview').attr('src', res.url);

                        // hiệu ứng nhẹ
                        $('#avatarPreview')
                            .css('opacity', 0.3)
                            .animate({ opacity: 1 }, 200);
                    }
                },
                error: function () {
                    $('.avatar-wrapper').removeClass('loading');
                    $('.avatar-error')
                        .removeClass('hidden')
                        .html("Lỗi xảy ra! Không thể upload ảnh.");
                }
            });
        });
    };

    $(document).ready(function(){
        
        HT.uploadAvatar();
        HT.buynow();
        HT.collapse()
        HT.changeStatusDropdownMenu()
        HT.changeStatusPass()
        HT.changeStatusChildren()

        HT.highlightTocOnScroll();
        HT.scrollHeading()
        HT.scroll()
        HT.addVoucher()
        HT.removePagination()

        
        /* CORE JS */
        HT.swiper()
        HT.niceSelect()		
        HT.select2()
        // HT.loadDistribution()
        HT.wrapTable()
        // HT.service()
        HT.skeleton()

        /** ACTION  */
        HT.register()
        HT.previewVideo()
        // HT.filterCourse()


        /** SLIDES */

        HT.major()
        HT.partner()
        HT.category()
        HT.product()
        HT.feedback()

        // $(window).on('load', function() {
        //     HT.swiper();
        // });
    });


})(jQuery);
