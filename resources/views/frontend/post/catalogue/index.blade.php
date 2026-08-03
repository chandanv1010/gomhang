@extends('frontend.homepage.layout')

@section('content')
    <div id="art-catalogue" class="page-body">
        
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
                </ul>
            </div>
        </div>

        <div class="art-catalogue-wrapper uk-margin-large-top uk-margin-large-bottom">
            <div class="uk-container uk-container-center">
                
                @if(false && $postCatalogue->canonical === 'du-an-tieu-bieu')
                    {{-- Projects List Style (Matches Homepage) --}}
                    <div class="panel-featured-projects" style="padding: 0;">
                        <div class="uk-grid uk-grid-large uk-grid-width-1-1 uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-4" data-uk-grid-margin>
                            @foreach($posts as $key => $val)
                                @php
                                    $title = $val->languages->first()->pivot->name;
                                    $image = $val->image;
                                    $href = write_url($val->languages->first()->pivot->canonical);
                                @endphp
                                <div class="mb30">
                                    <div class="project-card">
                                        <a href="{{ $href }}" class="card-image">
                                            <img src="{{ $image }}" alt="{{ $title }}">
                                        </a>
                                        <div class="card-body">
                                            <div class="project-info-list">
                                                {!! $val->languages->first()->pivot->description !!}
                                            </div>
                                            <a href="{{ $href }}" class="btn-xem-them">XEM THÊM</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($postCatalogue->canonical === 'video-tieu-bieu')
                    {{-- Videos List Style with Play Overlay --}}
                    <div class="uk-grid uk-grid-medium uk-grid-width-1-1 uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-3" data-uk-grid-margin>
                        @foreach($posts as $key => $val)
                            @php
                                $title = $val->languages->first()->pivot->name;
                                $image = $val->image;
                                $href = write_url($val->languages->first()->pivot->canonical);
                            @endphp
                            <div class="mb30">
                                <div class="premium-video-card wow fadeInUp" data-wow-delay="{{ ($key % 3) * 0.1 }}s">
                                    <div class="video-thumb-wrapper">
                                        <a href="{{ $href }}" class="image img-cover img-zoomin">
                                            <img src="{{ $image }}" alt="{{ $title }}">
                                        </a>
                                        <a href="{{ $href }}" class="play-btn-overlay">
                                            <i class="fa fa-play-circle"></i>
                                        </a>
                                    </div>
                                    <div class="video-info">
                                        <h3 class="video-title">
                                            <a href="{{ $href }}" title="{{ $title }}">{{ $title }}</a>
                                        </h3>
                                        <div class="video-meta">
                                            <span class="date"><i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($val->created_at)->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- News/Other Posts List Style --}}
                    <div class="uk-grid uk-grid-medium uk-grid-width-1-1 uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-large-1-3" data-uk-grid-margin>
                        @foreach($posts as $key => $val)
                            @php
                                $title = $val->languages->first()->pivot->name;
                                $image = $val->image;
                                $href = write_url($val->languages->first()->pivot->canonical);
                                $description = cutnchar(strip_tags($val->languages->first()->pivot->description), 150);
                            @endphp
                            <div class="mb30">
                                <div class="news-item wow fadeInUp" data-wow-delay="{{ ($key % 3) * 0.1 }}s">
                                    <a href="{{ $href }}" class="image img-cover img-zoomin" title="{{ $title }}">
                                        <img src="{{ $image }}" alt="{{ $title }}">
                                    </a>
                                    <div class="info" style="padding: 20px;">
                                        <div class="date-tag" style="font-size: 12px; color: #f37a20; margin-bottom: 8px;">
                                            <span class="dot" style="display: inline-block; width: 6px; height: 6px; background: #f37a20; border-radius: 50%; margin-right: 6px; vertical-align: middle;"></span>
                                            {{ \Carbon\Carbon::parse($val->created_at)->format('d/m/Y') }}
                                        </div>
                                        <h3 class="title" style="margin: 0 0 10px 0; font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 700; line-height: 1.4;">
                                            <a href="{{ $href }}" title="{{ $title }}" style="color: #222; text-decoration: none; transition: color 0.2s;">{{ $title }}</a>
                                        </h3>
                                        <div class="description" style="font-size: 13.5px; color: #666; line-height: 1.6; margin-bottom: 15px;">
                                            {!! $description !!}
                                        </div>
                                        <a href="{{ $href }}" class="btn-read-more-news" style="font-size: 13px; font-weight: 700; color: #1e4794; text-decoration: none; text-transform: uppercase;">
                                            Xem chi tiết &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="uk-flex uk-flex-center uk-margin-large-top">
                    @include('frontend.component.pagination', ['model' => $posts])
                </div>

            </div>
        </div>
    </div>
@endsection
