<div class="panel-body render">
    <div class="uk-grid uk-grid-medium">
        @foreach($products as $product)
            <div class="uk-width-1-2 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-4 mb20">  
                @php
                    $name = $product->name;
                    $canonical = write_url($product->canonical);
                    $image = thumb(image($product->image), 600, 400);
                    $price = getPrice($product);
                    $total_lesson = $product->total_lesson;
                    $duration = $product->duration; 
                    $lecturer_name = $product->lecturer_name ?? null;
                    $lecturer_avatar = $product->lecturer_avatar ?? null;
                    $review['star'] = ($product->review_count == 0) ? '0' : $product->review_average/5*100;
                @endphp
                <div class="product-item">
                    <a href="{{ $canonical }}" title="{{ $name }}" class="image img-scaledown img-zoomin">
                        <div class="skeleton-loading"></div>
                        <img class="lazy-image" data-src="{{ $image }}" src="{{ $image }}" alt="{{ $name }}">
                    </a>
                    <div class="info">
                        <div class="course">
                            <div class="uk-flex uk-flex-middle">
                                <div class="total-lesson uk-flex">
                                    <img src="/backend/img/lesson.svg" alt="">
                                    <span>{{ $total_lesson }} Bài học</span>
                                </div>
                                <div class="duration uk-flex">
                                    <img src="/backend/img/time.svg" alt="">
                                    <span>{{ $duration }}</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="title">
                            <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
                        </h3>
                        <div class="product-price">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                {!! $price['html'] !!}
                            </div>
                        </div>
                        <div class="info-lecturer">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <div class="if">
                                    <a href="" class="image img-cover">
                                        <img src="{{ $lecturer_avatar  }}" alt="">
                                    </a>
                                    <div class="text">
                                        <h4 class="heading-3"><span>{{ $lecturer_name }}</span></h4>
                                        <div class="rating">
                                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                <div>
                                                    <div class="star-rating">
                                                        <div class="stars" style="--star-width: {{ $review['star']  }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ $canonical }}" class="btn-detail">
                                    <span>Xem chi tiết</span>
                                    <svg width="4" height="6" viewBox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3.49014 2.59539L1.44887 0.169256C1.40412 0.115625 1.35087 0.073056 1.2922 0.0440061C1.23354 0.0149562 1.17061 0 1.10706 0C1.0435 0 0.980576 0.0149562 0.921909 0.0440061C0.863242 0.073056 0.809996 0.115625 0.765241 0.169256C0.675574 0.276465 0.625244 0.421491 0.625244 0.572658C0.625244 0.723826 0.675574 0.868851 0.765241 0.97606L2.4695 3.00165L0.765241 5.02725C0.675574 5.13446 0.625244 5.27948 0.625244 5.43065C0.625244 5.58182 0.675574 5.72684 0.765241 5.83405C0.810225 5.88708 0.863576 5.92904 0.922232 5.95752C0.980888 5.98599 1.0437 6.00043 1.10706 5.99999C1.17042 6.00043 1.23322 5.98599 1.29188 5.95752C1.35054 5.92904 1.40389 5.88708 1.44887 5.83405L3.49014 3.40792C3.53526 3.35472 3.57108 3.29144 3.59552 3.22171C3.61996 3.15198 3.63254 3.07719 3.63254 3.00165C3.63254 2.92612 3.61996 2.85133 3.59552 2.7816C3.57108 2.71187 3.53526 2.64858 3.49014 2.59539Z" fill="#F2277E"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>