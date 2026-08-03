@extends('frontend.homepage.layout')

@section('content')
    <div id="art-detail" class="page-body">
        
        {{-- Standard Hero Breadcrumb Banner --}}
        <div class="cat-hero-section" style="background-image: url('/vendor/frontend/img/project/breadcrumb.png');">
            <div class="cat-hero-overlay"></div>
            <div class="cat-hero-shapes">
                <div class="shape shape-left"></div>
                <div class="shape shape-right"></div>
            </div>
            <div class="uk-container uk-container-center cat-hero-container">
                <h1 class="cat-hero-title">{{ $postCatalogue->name }}</h1>
                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs">
                    <li><a href="/">Trang chủ</a></li>
                    @if(!is_null($breadcrumb))
                        @foreach($breadcrumb as $key => $val)
                            @php
                                $name = $val->languages->first()->pivot->name;
                                $canonical = write_url($val->languages->first()->pivot->canonical, true, true);
                            @endphp
                            <li class="separator">&raquo;</li>
                            <li><a href="{{ $canonical }}">{{ $name }}</a></li>
                        @endforeach
                    @endif
                    <li class="separator">&raquo;</li>
                    <li><a href="#" onclick="return false;">{{ \Illuminate\Support\Str::limit($post->name, 40) }}</a></li>
                </ul>
            </div>
        </div>

        @if($postCatalogue->canonical === 'du-an-tieu-bieu')
            {{-- ==================================================== --}}
            {{-- PROJECT DETAIL LAYOUT SLIDER PART                    --}}
            {{-- ==================================================== --}}
            
            {{-- Slider Full-width at the top --}}
            <div class="project-detail-slider-section uk-margin-large-top uk-margin-bottom">
                <div class="uk-container uk-container-center">
                    @php
                        $albumSource = is_array($post->album) ? $post->album : json_decode($post->album ?? '[]', true);
                        $list_image = array_values(array_filter(is_array($albumSource) ? $albumSource : []));
                        if (!empty($post->image)) {
                            array_unshift($list_image, $post->image);
                        }
                        $list_image = array_values(array_unique($list_image));
                    @endphp
                    @if(count($list_image))
                        <!-- Main Swiper -->
                        <div class="swiper-container project-main-swiper" style="height: 500px; width: 100%; overflow: hidden; position: relative;">
                            <div class="swiper-wrapper">
                                @foreach($list_image as $img)
                                    <div class="swiper-slide" style="width: 100%; height: 100%;">
                                        <div class="img-cover" style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #000;">
                                            <img src="{{ $img }}" alt="{{ $post->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Add Arrows -->
                            <div class="swiper-button-next" style="color: #fff;"></div>
                            <div class="swiper-button-prev" style="color: #fff;"></div>
                        </div>
                        
                        <!-- Thumbnail Swiper -->
                        <div class="swiper-container project-thumbs-swiper" style="margin-top: 15px; height: 100px; box-sizing: border-box; padding: 10px 0;">
                            <div class="swiper-wrapper">
                                @foreach($list_image as $img)
                                    <div class="swiper-slide" style="width: 25%; height: 100%; opacity: 0.4; cursor: pointer; transition: opacity 0.3s;">
                                        <img src="{{ $img }}" alt="thumb" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var galleryThumbs = new Swiper('.project-thumbs-swiper', {
                        spaceBetween: 10,
                        slidesPerView: 4,
                        freeMode: true,
                        watchSlidesVisibility: true,
                        watchSlidesProgress: true,
                    });
                    var galleryTop = new Swiper('.project-main-swiper', {
                        spaceBetween: 10,
                        autoplay: {
                            delay: 4000,
                            disableOnInteraction: false,
                        },
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        thumbs: {
                            swiper: galleryThumbs,
                        },
                    });
                    
                    // Sync active slide class for thumbs opacity
                    galleryTop.on('slideChange', function() {
                        var slides = document.querySelectorAll('.project-thumbs-swiper .swiper-slide');
                        slides.forEach(function(slide, idx) {
                            if (idx === galleryTop.activeIndex) {
                                slide.style.opacity = '1';
                            } else {
                                slide.style.opacity = '0.4';
                            }
                        });
                    });
                    // Initial set
                    setTimeout(function() {
                        var slides = document.querySelectorAll('.project-thumbs-swiper .swiper-slide');
                        if (slides.length) slides[0].style.opacity = '1';
                    }, 100);
                });
            </script>
        @endif

        {{-- Main Content 3/4 & Sidebar 1/4 layout for ALL posts (projects, news, and videos) --}}
        <div class="art-catalogue-wrapper uk-margin-large-top uk-margin-large-bottom">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-large">
                   
                    {{-- 3/4 Column Content --}}
                    <div class="uk-width-large-3-4">
                        <div class="art-detail">
                            <h1 style="font-family: 'UTM Avo', sans-serif !important; font-weight: bold !important; font-size: 32px; color: #222; margin-bottom: 20px; line-height: 1.3;">
                                {{ $post->name }}
                            </h1>
                            
                            <div class="post-meta-details uk-flex uk-flex-middle" style="margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; color: #888; font-size: 13px;">
                                <span class="date uk-margin-right"><i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($post->created_at)->format('d/m/Y H:i') }}</span>
                                <span class="views"><i class="fa fa-eye"></i> {{ $post->viewed }} lượt xem</span>
                            </div>

                            {{-- If it is a video, embed the video player at the top --}}
                            @if($postCatalogue->canonical === 'video-tieu-bieu' && !empty($post->video))
                                <div class="video-detail-player uk-margin-bottom">
                                    <div class="video-iframe-container" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; background: #000; border-radius: 8px;">
                                        {!! $post->video !!}
                                    </div>
                                </div>
                            @endif

                            {{-- If it is a project, render the project description in a nice info box --}}
                            @if($postCatalogue->canonical === 'du-an-tieu-bieu')
                                <div class="project-info-card project-info-card-in-content uk-margin-bottom" style="background: #fdfdfd; border: 1px solid #eee; padding: 25px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
                                    <h3 class="info-card-title" style="font-family: 'UTM Avo', sans-serif !important; font-weight: bold !important; font-size: 18px; color: #1e4794; text-transform: uppercase; border-bottom: 2px solid #f37a20; padding-bottom: 12px; margin-bottom: 20px; text-align: left;">
                                        Thông tin dự án
                                    </h3>
                                    <div class="project-meta-list" style="font-size: 14px; line-height: 1.8; color: #555; text-align: left;">
                                        {!! $post->description !!}
                                    </div>
                                </div>
                            @else
                                <div class="description uk-text-lead" style="font-family: 'Manrope', sans-serif !important; font-size: 16px; line-height: 1.8; color: #555; font-weight: 600; margin-bottom: 30px;">
                                    {!! $post->description !!}
                                </div>
                            @endif

                            <div class="content-body" style="font-family: 'Manrope', sans-serif !important; font-size: 15px; line-height: 1.8; color: #444; margin-bottom: 40px;">
                                {!! $post->content !!}
                            </div>

                            @if (isset($postCatalogue->posts) && !is_null($postCatalogue->posts))
                                <div class="artdetail-relate style-1 mt30">
                                    <div class="heading-1 mb10">
                                        <span>Bài viết liên quan</span>
                                    </div>

                                    <ul class="uk-list uk-clearfix uk-grid uk-grid-medium uk-grid-width-medium-1-2 uk-grid-width-large-1-3 list-related">
                                        @foreach ($postCatalogue->posts as $key => $val)
                                            @php
                                                if($val->id === $post->id) continue; 
                                                $title = $val->languages->first()->pivot->name;
                                                $image = $val->image;
                                                $href  = write_url($val->languages->first()->pivot->canonical);
                                            @endphp

                                            <li class="mb10">
                                                <article class="article">
                                                    <div class="thumb">
                                                        <a href="{{ $href }}" title="{{ $title }}" class="image img-cover img-zoomin">
                                                            <img src="{{ $image }}" alt="{{ $title }}" />
                                                        </a>
                                                    </div>
                                                    <h3 class="title">
                                                        <a href="{{ $href }}" title="{{ $title }}">
                                                            {{ $title }}
                                                        </a>
                                                    </h3>
                                                </article>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- 1/4 Column Sidebar --}}
                    <div class="uk-width-large-1-4">
                        <div class="sidebar-detail">
                            
                            <!-- DỰ ÁN NỔI BẬT widget -->
                            @php
                                $featuredProjectWidget = $widgets['featured-project'] ?? null;
                                $projectCat = (isset($featuredProjectWidget->object) && $featuredProjectWidget->object->isNotEmpty()) ? $featuredProjectWidget->object->first() : null;
                                $projectPosts = $projectCat ? $projectCat->posts->take(5) : collect();
                            @endphp
                            @if($projectPosts->isNotEmpty())
                                <div class="sidebar-widget uk-margin-large-bottom">
                                    <h3 class="widget-title">DỰ ÁN NỔI BẬT</h3>
                                    <ul class="uk-list widget-posts-list">
                                        @foreach($projectPosts as $wPost)
                                            @php
                                                $wLang = $wPost->languages->first();
                                            @endphp
                                            <li class="uk-flex uk-flex-middle uk-margin-bottom">
                                                <a href="{{ write_url($wLang->canonical) }}" class="widget-post-thumb uk-margin-right">
                                                    <img src="{{ $wPost->image }}" alt="{{ $wLang->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                                                </a>
                                                <div class="widget-post-info">
                                                    <h4 class="post-title" style="margin: 0; font-size: 13px; font-weight: 600; line-height: 1.4;">
                                                        <a href="{{ write_url($wLang->canonical) }}" style="color: #333; text-decoration: none;">{{ $wLang->name }}</a>
                                                    </h4>
                                                    <div class="post-excerpt" style="font-size: 11px; color: #777; margin-top: 4px;">
                                                        {{ cutnchar(strip_tags($wLang->description), 50) }}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- TIN TỨC NỔI BẬT widget -->
                            @php
                                $newsWidget = $widgets['homepage-news'] ?? null;
                                $newsCat = (isset($newsWidget->object) && $newsWidget->object->isNotEmpty()) ? $newsWidget->object->first() : null;
                                $newsPosts = $newsCat ? $newsCat->posts->take(5) : collect();
                            @endphp
                            @if($newsPosts->isNotEmpty())
                                <div class="sidebar-widget uk-margin-large-bottom">
                                    <h3 class="widget-title">TIN TỨC NỔI BẬT</h3>
                                    <ul class="uk-list widget-posts-list">
                                        @foreach($newsPosts as $wPost)
                                            @php
                                                $wLang = $wPost->languages->first();
                                            @endphp
                                            <li class="uk-flex uk-flex-middle uk-margin-bottom">
                                                <a href="{{ write_url($wLang->canonical) }}" class="widget-post-thumb uk-margin-right">
                                                    <img src="{{ $wPost->image }}" alt="{{ $wLang->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                                                </a>
                                                <div class="widget-post-info">
                                                    <h4 class="post-title" style="margin: 0; font-size: 13px; font-weight: 600; line-height: 1.4;">
                                                        <a href="{{ write_url($wLang->canonical) }}" style="color: #333; text-decoration: none;">{{ $wLang->name }}</a>
                                                    </h4>
                                                    <div class="post-excerpt" style="font-size: 11px; color: #777; margin-top: 4px;">
                                                        {{ cutnchar(strip_tags($wLang->description), 50) }}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- VIDEO NỔI BẬT widget -->
                            @php
                                $videoWidget = $widgets['homepage-video'] ?? null;
                                $videoCat = (isset($videoWidget->object) && $videoWidget->object->isNotEmpty()) ? $videoWidget->object->first() : null;
                                $videoPosts = $videoCat ? $videoCat->posts->take(5) : collect();
                            @endphp
                            @if($videoPosts->isNotEmpty())
                                <div class="sidebar-widget">
                                    <h3 class="widget-title">VIDEO NỔI BẬT</h3>
                                    <ul class="uk-list widget-posts-list">
                                        @foreach($videoPosts as $wPost)
                                            @php
                                                $wLang = $wPost->languages->first();
                                            @endphp
                                            <li class="uk-flex uk-flex-middle uk-margin-bottom">
                                                <a href="{{ write_url($wLang->canonical) }}" class="widget-post-thumb uk-margin-right" style="position: relative;">
                                                    <img src="{{ $wPost->image }}" alt="{{ $wLang->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px;">
                                                    <i class="fa fa-play-circle" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #fff; font-size: 18px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);"></i>
                                                </a>
                                                <div class="widget-post-info">
                                                    <h4 class="post-title" style="margin: 0; font-size: 13px; font-weight: 600; line-height: 1.4;">
                                                        <a href="{{ write_url($wLang->canonical) }}" style="color: #333; text-decoration: none;">{{ $wLang->name }}</a>
                                                    </h4>
                                                    <div class="post-excerpt" style="font-size: 11px; color: #777; margin-top: 4px;">
                                                        {{ cutnchar(strip_tags($wLang->description), 50) }}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>

    </div>
@endsection
