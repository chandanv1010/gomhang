@extends('frontend.homepage.layout')
@php
    $a = $introduce;
    $section_array_1 = [
        ['icon' => 'Ten-tieng-Trung-1.png', 'name' => 'Tên tiếng Trung', 'value' => $introduce['block_1_title']],
        ['icon' => 'Tax.png', 'name' => 'Mã số thuế', 'value' => $introduce['block_1_tax']],
        ['icon' => 'Nam-hoat-dong-1.png', 'name' => 'Năm hoạt động', 'value' => $introduce['block_1_year']],
        ['icon' => 'Top-1-Viet-Nam.png', 'name' => 'Xếp hạng dịch vụ', 'value' => $introduce['block_1_rank']],
        ['icon' => 'Ten-tieng-Trung-1.png', 'name' => '', 'value' => $introduce['block_1_en']],
        ['icon' => 'Email.png', 'name' => 'Email', 'value' => $introduce['block_1_email']],
        ['icon' => 'Hotline.png', 'name' => 'Hotline', 'value' => $introduce['block_1_hotline']],
        ['icon' => 'Hop-tac.png', 'name' => 'Hợp tác', 'value' => $introduce['block_1_connect_count']],
    ];
@endphp
@section('content')
    <div class="intro-container">
        <div class="section-1">
            <div class="uk-container uk-container-center">
                <h1 class="heading-10"><span>{{ $introduce['block_1_company'] }}</span></h1>
                <div class="description">{!! $introduce['block_1_description'] !!}</div>
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    <div class="uk-width-large-1-3">
                        @foreach($section_array_1 as $key => $val)
                        @if($key > 3) @break @endif
                        <article class="a-item">
                            <div class="uk-flex uk-flex-middle">
                                <div class="icon"><img src="{{ asset('vendor/frontend/img/project/'.$val['icon']) }}" alt="Icon"></div>
                                <div class="title">
                                    @if(!empty($val['name']))
                                    <div class="spec-text">{{ $val['name'] }}</div>
                                    @endif
                                    <div class="spec-value">{{ $val['value'] }}</div>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                    <div class="uk-width-large-1-3">
                        <div class="section-1-image">
                            <span class="image img-cover img-zoomin"><img src="{{ asset($introduce['block_1_image']) }}" alt=""></span>
                        </div>
                        <div class="visit-link"><a href="{{ $introduce['block_1_fanpage_link'] }}" target="_blank">Truy cập vào Fanpage của chúng tôi</a></div>
                    </div>
                    <div class="uk-width-large-1-3">
                         @foreach($section_array_1 as $key => $val)
                            @if($key <=3) @continue @endif
                            <article class="a-item">
                                <div class="uk-flex uk-flex-middle">
                                    <div class="icon"><img src="{{ asset('vendor/frontend/img/project/'.$val['icon']) }}" alt="Icon"></div>
                                    <div class="title">
                                        @if(!empty($val['name']))
                                        <div class="spec-text">{{ $val['name'] }}</div>
                                        @endif
                                        <div class="spec-value">{{ $val['value'] }}</div>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="section-2">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    <div class="uk-width-medium-1-2">
                        <div class="number-container">
                            <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                                @for($i = 1; $i <= 6; $i++)
                                <div class="uk-width-small-1-1 uk-width-medium-1-2 mb20">
                                    <div class="number-box-item color_{{ $i }}">
                                        <span class="int">{{ $introduce['block_1_box_'.$i.'_number'] }}</span>
                                        <div class="name">{{ $introduce['block_1_box_'.$i.'_text'] }}</div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-medium-1-2">
                        <div class="description">
                            {!! $introduce['block_1_content'] !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-3">
            <div class="uk-container uk-container-center">
                <h2 class="heading-10"><span>{{ $introduce['block_3_heading'] }}</span></h2>
                <div class="description">{{ $introduce['block_3_description'] }}</div>
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="uk-width-large-1-3 image-{{ $i }}">
                        <span class="image img-cover"><img src="{{ asset($introduce['block_3_image_'.$i]) }}" alt="image"></span>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
        <div class="section-4">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    <div class="uk-width-medium-2-5">
                        <div class="section-4-container">
                            <h2 class="heading-9"><span>{{ $a['block_4_heading'] }}</span></h2>
                            <div class="description">{!! $a['block_4_description'] !!}</div>
                            <div class="video">
                                {!! $a['block_4_video'] !!}
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-medium-3-5">
                        <span class="image img-zoomin img-cover">
                            <img src="{{ $a['block_4_image'] }}" alt="icon">
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-5">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    <div class="uk-width-medium-2-5">
                        <span class="image img-cover"><img src="{{ $a['block_5_image'] }}" alt="image"></span>
                    </div>
                    <div class="uk-width-medium-3-5">
                        <div class="section-5-container">
                            <h3 class="small-text">{{ $a['block_5_small_heading'] }}</h3>
                            <h2 class="heading-10"><span>{{ $a['block_5_heading'] }}</span></h2>
                            <div class="description">
                                {!! $a['block_5_description'] !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-6">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                    <div class="uk-width-large-1-2">
                        <div class="section-6-container">
                            <h2 class="heading-8"><span>{{ $a['block_6_heading'] }}</span></h2>
                            <div class="description">{!! $a['block_6_description'] !!}</div>
                            <div class="list-sec">
                                @for($i = 1; $i <= 3; $i++)
                                <article class="sec-item item-{{$i}}">
                                    <span class="icon"><i class="fa fa-star"></i></span>
                                    <div class="description">
                                        <div class="title">{{ $a['block_6_block_'.$i.'_title'] }}</div>
                                        <div class="description">
                                            {{ $a['block_6_block_'.$i.'_description'] }}
                                        </div>
                                    </div>
                                </article>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-large-1-2">
                        <span class="image img-cover"><img src="{{ $a['block_6_image'] }}" alt="icobn"></span>
                    </div>
                </div>
            </div>
        </div>
         <div class="section-6">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle">
                     <div class="uk-width-large-1-2">
                        <span class="image img-cover"><img src="{{ $a['block_7_image'] }}" alt="icobn"></span>
                    </div>
                    <div class="uk-width-large-1-2">
                        <div class="section-6-container">
                            <h2 class="heading-8"><span>{{ $a['block_7_heading'] }}</span></h2>
                            <div class="description">{!! $a['block_7_description'] !!}</div>
                            <div class="list-sec">
                                @for($i = 1; $i <= 2; $i++)
                                <article class="sec-item item-{{$i}}">
                                    <span class="icon"><i class="fa fa-star"></i></span>
                                    <div class="description">
                                        <div class="title">{{ $a['block_7_block_'.$i.'_title'] }}</div>
                                        <div class="description">
                                            {{ $a['block_7_block_'.$i.'_description'] }}
                                        </div>
                                    </div>
                                </article>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $icon = ['fa fa-user', 'fa fa-diamond', 'fa fa-tag']
        @endphp
        <div class="section-9">
            <div class="uk-container uk-container-center">
                <h2 class="heading-9"><span>{{ $a['block_9_heading'] }}</span></h2>
                <div class="description-b">
                    {!! $a['block_9_description'] !!}
                </div>
                <div class="why-list">
                    <div class="uk-grid uk-grid-medium">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="uk-width-large-1-3">
                            <div class="i-why-item">
                                <div class="icon"><i class="{{ $icon[$i - 1] }}"></i></div>
                                <div class="title">{{ $a['block_9_block_'.$i.'_title'] }}</div>
                                <div class="description">
                                    {!! $a['block_9_block_'.$i.'_description'] !!}
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
        <div class="section-8">
            <div class="uk-container uk-container-center">
                <h2 class="heading-10"><span>{{ $a['block_8_heading'] }}</span></h2>
                <div class="description text-center">{!! $a['block_8_description'] !!}</div>
                <div class="uk-grid uk-grid-medium">
                    <div class="uk-width-large-1-3">
                        <div class="item-a i-service-item">
                            <div class="title">{{ $a['block_8_block_1_title'] }}</div>
                            <div class="description">{!! $a['block_8_block_1_description'] !!}</div>
                        </div>
                    </div>
                    <div class="uk-width-large-2-3">
                        <div class="service-list">
                            <div class="uk-grid uk-grid-medium">
                                @for($i = 2; $i<=5; $i++)
                                <div class="uk-width-large-1-2 mb30">
                                    <div class="i-service-item">
                                        <div class="title">{{ $a['block_8_block_'.$i.'_title'] }}</div>
                                        <div class="description">
                                            {{ $a['block_8_block_'.$i.'_description'] }}
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>


   
@endsection

