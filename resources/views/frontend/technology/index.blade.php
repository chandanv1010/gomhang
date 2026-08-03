@extends('frontend.homepage.layout')
@section('content')

    {{-- Main technology wrapper --}}
    <section class="technology-page-wrapper">
        
        {{-- Section 1 & 2: Banner with Overlapping Icons --}}
        <div class="tech-banner-section" style="background-image: url('{{ $introduces['block_1_banner_image'] ?? '/vendor/frontend/img/banner-fallback.jpg' }}');">
            <div class="tech-banner-overlay"></div>
            
            <div class="uk-container uk-container-center tech-banner-container">
                {{-- Text Overlay Box (Glassmorphism) --}}
                <div class="tech-text-box">
                    <h2 class="tech-title">{{ $introduces['block_1_banner_title'] ?? 'Công Nghệ Độc Quyền TexGuard' }}</h2>
                    <p class="tech-desc">{{ $introduces['block_1_banner_desc'] ?? 'Với vị thế tạo nên xu hướng công nghệ đột phá toàn cầu, Quy trình xử lý bề mặt Thanh nhôm MAXPRO.JP được áp dụng công nghệ độc quyền TexGuard.' }}</p>
                </div>
            </div>

            {{-- 4 Floating Icons Section (positioned absolutely at the bottom border) --}}
            <div class="tech-icons-bar-wrapper">
                <div class="uk-container uk-container-center">
                    <div class="uk-grid uk-grid-collapse uk-child-width-1-4@l uk-child-width-1-2@m uk-child-width-1-1 tech-icons-grid">
                        
                        {{-- Icon Card 1 --}}
                        <div class="tech-icon-col">
                            <div class="tech-icon-card">
                                <div class="hex-icon-wrapper">
                                    <div class="hex-icon-bg">
                                        <img src="{{ $introduces['block_2_icon_1'] ?? '/vendor/frontend/img/icon-1.png' }}" alt="Icon 1">
                                    </div>
                                </div>
                                @if(!empty($introduces['block_2_title_1']))
                                    <h4 class="card-title">{{ $introduces['block_2_title_1'] }}</h4>
                                @endif
                            </div>
                        </div>

                        {{-- Icon Card 2 --}}
                        <div class="tech-icon-col">
                            <div class="tech-icon-card">
                                <div class="hex-icon-wrapper">
                                    <div class="hex-icon-bg">
                                        <img src="{{ $introduces['block_2_icon_2'] ?? '/vendor/frontend/img/icon-2.png' }}" alt="Icon 2">
                                    </div>
                                </div>
                                @if(!empty($introduces['block_2_title_2']))
                                    <h4 class="card-title">{{ $introduces['block_2_title_2'] }}</h4>
                                @endif
                            </div>
                        </div>

                        {{-- Icon Card 3 --}}
                        <div class="tech-icon-col">
                            <div class="tech-icon-card">
                                <div class="hex-icon-wrapper">
                                    <div class="hex-icon-bg">
                                        <img src="{{ $introduces['block_2_icon_3'] ?? '/vendor/frontend/img/icon-3.png' }}" alt="Icon 3">
                                    </div>
                                </div>
                                @if(!empty($introduces['block_2_title_3']))
                                    <h4 class="card-title">{{ $introduces['block_2_title_3'] }}</h4>
                                @endif
                            </div>
                        </div>

                        {{-- Icon Card 4 --}}
                        <div class="tech-icon-col">
                            <div class="tech-icon-card">
                                <div class="hex-icon-wrapper">
                                    <div class="hex-icon-bg">
                                        <img src="{{ $introduces['block_2_icon_4'] ?? '/vendor/frontend/img/icon-4.png' }}" alt="Icon 4">
                                    </div>
                                </div>
                                @if(!empty($introduces['block_2_title_4']))
                                    <h4 class="card-title">{{ $introduces['block_2_title_4'] }}</h4>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Accordion Section --}}
        <div class="tech-accordion-section">
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-large uk-flex-middle" data-uk-grid-margin>
                    
                    {{-- Left Image Column --}}
                    <div class="uk-width-large-1-2 uk-width-medium-1-1 uk-width-1-1">
                        <div class="accordion-img-wrapper">
                            <img src="{{ $introduces['block_3_image'] ?? '/vendor/frontend/img/acc-showcase.jpg' }}" alt="Showcase Image" class="acc-showcase-img">
                        </div>
                    </div>

                    {{-- Right Accordion Column --}}
                    <div class="uk-width-large-1-2 uk-width-medium-1-1 uk-width-1-1">
                        <div class="accordion-content-wrapper">
                            <h2 class="accordion-main-title">{{ $introduces['block_3_title'] ?? 'Ưu Điểm Vượt Trội' }}</h2>
                            
                            <div class="tech-accordion-container">
                                
                                {{-- Item 1 --}}
                                @if(!empty($introduces['block_3_acc_title_1']))
                                <div class="accordion-item active">
                                    <div class="accordion-header">
                                        <span class="header-text">{{ $introduces['block_3_acc_title_1'] }}</span>
                                        <span class="header-icon"><i class="fa fa-chevron-down"></i></span>
                                    </div>
                                    <div class="accordion-body" style="display: block;">
                                        <div class="accordion-inner">
                                            {!! $introduces['block_3_acc_content_1'] ?? 'Mô tả ưu điểm vượt trội thứ nhất.' !!}
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Item 2 --}}
                                @if(!empty($introduces['block_3_acc_title_2']))
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <span class="header-text">{{ $introduces['block_3_acc_title_2'] }}</span>
                                        <span class="header-icon"><i class="fa fa-chevron-down"></i></span>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="accordion-inner">
                                            {!! $introduces['block_3_acc_content_2'] ?? 'Mô tả ưu điểm vượt trội thứ hai.' !!}
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Item 3 --}}
                                @if(!empty($introduces['block_3_acc_title_3']))
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <span class="header-text">{{ $introduces['block_3_acc_title_3'] }}</span>
                                        <span class="header-icon"><i class="fa fa-chevron-down"></i></span>
                                    </div>
                                    <div class="accordion-body">
                                        <div class="accordion-inner">
                                            {!! $introduces['block_3_acc_content_3'] ?? 'Mô tả ưu điểm vượt trội thứ ba.' !!}
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </section>

    {{-- Technology Specific Styles --}}
    <style>
        .technology-page-wrapper {
            background-color: #fff;
            position: relative;
        }

        /* BANNER SECTION */
        .tech-banner-section {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            min-height: 520px;
            padding: 80px 0 160px;
            display: flex;
            align-items: center;
        }

        .tech-banner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.25);
            z-index: 1;
        }

        .tech-banner-container {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        /* Glassmorphism Text Box */
        .tech-text-box {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 8px;
            padding: 25px 35px;
            max-width: 480px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            animation: fadeInLeft 0.8s ease-out;
        }

        .tech-title {
            font-family: 'Manrope', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #222;
            margin-top: 0;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .tech-desc {
            font-size: 14.5px;
            color: #555;
            line-height: 1.6;
            margin: 0;
            font-weight: 500;
        }

        /* FLOATING ICONS BAR */
        .tech-icons-bar-wrapper {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 3;
        }

        .tech-icons-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
        }

        .tech-icon-col {
            padding: 0 10px;
            width: 25%;
        }

        .tech-icon-card {
            background: transparent;
            border-radius: 0;
            padding: 0;
            text-align: center;
            box-shadow: none;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tech-icon-card:hover {
            transform: translateY(-4px);
        }

        /* Hexagonal icon wrapper */
        .hex-icon-wrapper {
            width: 80px;
            height: 80px;
            position: relative;
            background: #cbd5e1;
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            -webkit-clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -40px;
            margin-bottom: 20px;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05));
            transition: all 0.3s ease;
        }

        .hex-icon-bg {
            background: #fff;
            width: calc(100% - 2px);
            height: calc(100% - 2px);
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            -webkit-clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .tech-icon-card:hover .hex-icon-wrapper {
            background: #d63031;
            transform: scale(1.05);
        }

        .hex-icon-bg img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: #222 !important;
            margin-top: 0;
            margin-bottom: 0;
            line-height: 1.5;
            max-width: 220px;
            margin-left: auto;
            margin-right: auto;
            font-family: var(--second-font), sans-serif !important;
        }

        .card-desc {
            display: none;
        }

        /* ACCORDION SECTION */
        .tech-accordion-section {
            padding: 160px 0 90px;
            background-color: #fafbfc;
        }

        .accordion-img-wrapper {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #eceff1;
        }

        .acc-showcase-img {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }

        .accordion-img-wrapper:hover .acc-showcase-img {
            transform: scale(1.03);
        }

        .accordion-content-wrapper {
            padding-left: 20px;
        }

        .accordion-main-title {
            font-family: 'Manrope', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #222;
            margin-top: 0;
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 12px;
        }

        .accordion-main-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #f37a20;
            border-radius: 2px;
        }

        /* Accordion component */
        .tech-accordion-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .accordion-item {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #eef0f2;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .accordion-item.active {
            border-color: rgba(243, 122, 32, 0.3);
            box-shadow: 0 5px 15px rgba(243, 122, 32, 0.05);
        }

        .accordion-header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            transition: background 0.2s;
        }

        .accordion-header:hover {
            background-color: #fcfdfe;
        }

        .header-text {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            transition: color 0.2s;
        }

        .accordion-item.active .header-text {
            color: #d63031; /* red text on active header */
        }

        .header-icon {
            font-size: 12px;
            color: #777;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .accordion-item.active .header-icon {
            transform: rotate(180deg);
            color: #d63031;
        }

        .accordion-body {
            display: none;
            border-top: 1px solid #f5f6f7;
        }

        .accordion-inner {
            padding: 20px 24px;
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        .accordion-inner p {
            margin-top: 0;
            margin-bottom: 12px;
        }
        .accordion-inner p:last-child {
            margin-bottom: 0;
        }

        /* Keyframes */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* RESPONSIVE MEDIA QUERIES */
        @media (max-width: 959px) {
            .tech-banner-section {
                padding: 60px 0 100px;
                min-height: 400px;
            }
            
            .tech-text-box {
                margin: 0 auto;
                max-width: 100%;
            }

            .tech-icons-bar-wrapper {
                position: relative;
                top: auto;
                bottom: auto;
                margin-top: 40px;
                padding: 0 15px;
            }

            .tech-icon-col {
                width: 50%;
                margin-bottom: 20px;
            }

            .hex-icon-wrapper {
                margin-top: 0;
            }

            .tech-accordion-section {
                padding: 60px 0 60px;
            }

            .accordion-content-wrapper {
                padding-left: 0;
                margin-top: 30px;
            }
        }

        @media (max-width: 639px) {
            .tech-icon-col {
                width: 100%;
            }
            
            .tech-icons-bar-wrapper {
                margin-top: 20px;
            }
        }
    </style>

    {{-- jQuery Accordion Logic --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            $('.accordion-header').on('click', function() {
                var $item = $(this).closest('.accordion-item');
                
                // If it is already active, close it
                if ($item.hasClass('active')) {
                    $item.removeClass('active');
                    $item.find('.accordion-body').slideUp(250);
                } else {
                    // Close other open items
                    $('.accordion-item').removeClass('active');
                    $('.accordion-body').slideUp(250);
                    
                    // Open clicked item
                    $item.addClass('active');
                    $item.find('.accordion-body').slideDown(250);
                }
            });
        });
    </script>

@endsection
