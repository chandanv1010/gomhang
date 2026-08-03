(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;
    var _token = $('meta[name="csrf-token"]').attr('content');


	HT.productSwiperSlide = () => {
		document.querySelectorAll(".product-gallery").forEach(product => {
			var swiper = new Swiper(product.querySelector(".swiper-container"), {
				loop: true,
				autoHeight: false,
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
						el: product.querySelector('.swiper-container-thumbs'),
						slidesPerView: 8,
						spaceBetween: 10,
						slideToClickedSlide: true,
					}
				},
				on: {
					init: function() {
						// Đảm bảo height không bị Swiper override
						var container = product.querySelector(".swiper-container");
						var wrapper = product.querySelector(".swiper-wrapper");
						var slide = product.querySelector(".swiper-slide");
						if (container) container.style.height = 'auto';
						if (wrapper) wrapper.style.height = 'auto';
						if (slide) slide.style.height = 'auto';
					}
				}
			});
		});
	}

	HT.changeQuantity = () => {
		
		$(document).on('click','.quantity-button', function(){
			let _this = $(this)
			let quantity = $('.quantity-text').val()
			let newQuantity = 0
			if(_this.hasClass('minus')){
				 newQuantity =  quantity - 1
			}else{
				 newQuantity = parseInt(quantity) + 1
			}
			if(newQuantity < 1){
				newQuantity = 1
			}
			$('.quantity-text').val(newQuantity)
		})

	}

	HT.selectVariantProduct = () => {
		if($('.choose-attribute').length){
			$(document).on('click', '.choose-attribute', function(e){
				e.preventDefault()
				let _this = $(this)
				let attribute_id = _this.attr('data-attributeid')
				let attribute_name = _this.text()
				_this.parents('.attribute-item').find('span').html(attribute_name)
				_this.parents('.attribute-value').find('.choose-attribute').removeClass('active')
				_this.addClass('active')
				HT.handleAttribute();
			})
		}
	}

	HT.handleAttribute = () => {
		let attribute_id = []
		let flag = true
		$('.attribute-value .choose-attribute').each(function(){
			let _this = $(this)
			if(_this.hasClass('active')){
				attribute_id.push(_this.attr('data-attributeid'))
			}
		})

		$('.attribute').each(function(){
			if($(this).find('.choose-attribute.active').length === 0){
				flag = false
				return false;
			}
		})


		if(flag){
			$.ajax({
				url: 'ajax/product/loadVariant', 
				type: 'GET', 
				data: {
					'attribute_id' : attribute_id,
					'product_id' : $('input[name=product_id]').val(),
					'language_id' : $('input[name=language_id]').val(),
				}, 
				dataType: 'json', 
				beforeSend: function() {
					
				},
				success: function(res) {
					HT.setUpVariantPrice(res)
					HT.setupVariantGallery(res)
					HT.setupVariantName(res)
					HT.setupVariantUrl(res, attribute_id)
				},
			});
		}
	}

	HT.setupVariantUrl = (res, attribute_id) => {
		let queryString = '?attribute_id=' + attribute_id.join(',')
		let productCanonical = $('.productCanonical').val()
		productCanonical = productCanonical + queryString
		let stateObject = { attribute_id: attribute_id };
		history.pushState(stateObject, "Page Title", productCanonical);
	}

	HT.setUpVariantPrice = (res) => {
		$('.popup-product .price').html(res.variantPrice.html)
	}

	HT.setupVariantName = (res) => {
		let productName = $('.productName').val()
		let productVariantName = productName + ' ' + res.variant.languages[0].pivot.name
		$('.product-main-title span').html(productVariantName)
	}

	HT.setupVariantGallery = (gallery) => {
		let album = gallery.variant.album.split(',')

		if(album[0] == 0){
			album = JSON.parse($('input[name=product_gallery]').val())
		}

		console.log(album);

		let html = `<div class="swiper-container">
			<div class="swiper-button-next"></div>
			<div class="swiper-button-prev"></div>
			<div class="swiper-wrapper big-pic">`
			album.forEach((val) => {
				html += ` <div class="swiper-slide" data-swiper-autoplay="2000">
					<a href="${val}" data-uk-lightbox="{group:'my-group'}" class="image img-scaledown"><img src="${val}" alt="${val}"></a>
				</div>`
			})

			html += `</div>
			<div class="swiper-pagination"></div>
		</div>
		<div class="swiper-container-thumbs">
			<div class="swiper-wrapper pic-list">`;
		
		album.forEach((val) => {
			html += ` <div class="swiper-slide">
				<span class="image img-scaledown"><img src="${val}" alt="${val}"></span>
			</div>`
		})

		html += `</div>
		</div>`

		$('.popup-gallery').html(html)
		HT.popupSwiperSlide()
			
	}

	HT.chooseReviewStar = () => {
		$(document).on('click', '.popup-rating label', function(){
			let _this = $(this)
			let title = _this.attr('title')
			$('.rate-text').removeClass('uk-hidden').html(title)
		})
	}

    HT.quickConsult = () => {
        $(document).on('click', '.quick-consult-form button', function(e){
            e.preventDefault()
            let phone =  $('.quick-consult-form input[name=phone]').val()
            let product_id =  $('.quick-consult-form').data('id')
            if (!phone || !/^(0[3|5|7|8|9][0-9]{8})$/.test(phone)) {
                alert('Vui lòng nhập số điện thoại hợp lệ (10 chữ số, bắt đầu bằng 0).');
                return;
            }
            toastr.success('Gửi thông tin thành công. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất', 'Thông báo từ hệ thống !')
            $.ajax({
				url: 'ajax/contact/quickConsult', 
				type: 'POST', 
				data: {
					'phone' : phone,
                    'product_id' : product_id,
                    'post_id': $('input[name=post_id]').val(),
                    '_token' : _token
				}, 
				dataType: 'json', 
				success: function(res) {
					if(res.status == true){
                        
                        $('.quick-consult-form input[name=phone]').val('')
                    }
				},
			});
        })
    }

	$(document).ready(function(){
		/* CORE JS */
        HT.quickConsult()
		HT.changeQuantity()
		HT.productSwiperSlide()
		HT.selectVariantProduct()
		// HT.loadProductVariant()
		HT.chooseReviewStar()
	});

})(jQuery);

