
@if(isset($gallery) && !empty($gallery) && !is_null($gallery))
    <div class="product-gallery">
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-container">
            <div class="swiper-wrapper big-pic">
                <?php foreach($gallery as $key => $val){  ?>
                    <div class="swiper-slide" data-swiper-autoplay="2000">
                        {{-- <a href="{{ $val }}" data-uk-lightbox="{group:'my-group'}" class="image img-cover">
                            <img src="{{ image($val) }}" alt="<?php echo $val ?>">
                        </a> --}}
                        <a href="{{ $val }}" data-fancybox="my-group" class="image img-cover">
                            <img src="{{ image($val) }}" alt="<?php echo $val ?>">
                        </a>
                    </div>
                <?php }  ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
        <div class="swiper-container-thumbs">
            <div class="swiper-wrapper pic-list">
                <?php foreach($gallery as $key => $val){  ?>
                <div class="swiper-slide">
                    <span  class="image img-cover"><img src="{{  image($val) }}" alt="<?php echo $val ?>"></span>
                </div>
                <?php }  ?>
            </div>
        </div>
    </div>
@endif