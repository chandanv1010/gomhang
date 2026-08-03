@extends('frontend.homepage.layout')

@section('content')
<div class="about-wrapper">
    <!-- Section 1: Hero Banner & Thống kê -->
    @php
        $bannerBg = $introduces['block_1_banner_bg'] ?? '/userfiles/image/commit/Vector.png';
        $bannerTitle = $introduces['block_1_banner_title'] ?? 'VIREX VIỆT NAM';
        
        $statNum1 = $introduces['block_1_stat_num_1'] ?? '100+';
        $statLbl1 = $introduces['block_1_stat_lbl_1'] ?? 'Chuyên gia';
        
        $statNum2 = $introduces['block_1_stat_num_2'] ?? '10+';
        $statLbl2 = $introduces['block_1_stat_lbl_2'] ?? 'năm kinh nghiệm';
        
        $statNum3 = $introduces['block_1_stat_num_3'] ?? '1';
        $statLbl3 = $introduces['block_1_stat_lbl_3'] ?? 'Tổ chức hàng đầu';
        
        $btnText1 = $introduces['block_1_btn_text_1'] ?? 'Chuyên gia tư vấn';
        $btnLink1 = $introduces['block_1_btn_link_1'] ?? '#';
        
        $btnText2 = $introduces['block_1_btn_text_2'] ?? 'Sản phẩm';
        $btnLink2 = $introduces['block_1_btn_link_2'] ?? '/san-pham.html';
    @endphp
    
    <div class="about-banner-section" style="background-image: url('{{ $bannerBg }}');">
        <div class="uk-container uk-container-center about-banner-container">
            <div class="banner-inner">
                <!-- Breadcrumbs -->
                <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs" style="color: rgba(255, 255, 255, 0.85); margin-bottom: 12px; font-size: 14px; font-family: 'Manrope', sans-serif;">
                    <li><a href="/" style="color: rgba(255, 255, 255, 0.85); text-decoration: none;">Trang chủ</a></li>
                    <li style="margin: 0 8px; color: rgba(255, 255, 255, 0.85);">/</li>
                    <li style="color: #ffffff; font-weight: 700;">Giới thiệu</li>
                </ul>

                <h1 class="main-title wow fadeInUp" data-wow-delay="0.1s" style="margin-bottom: 40px;">{!! html_entity_decode($bannerTitle) !!}</h1>
                
                <!-- Stats Row (Moved above buttons) -->
                <div class="stats-row uk-grid uk-grid-width-medium-1-3 uk-grid-width-1-1" data-uk-grid-margin style="border-top: none; padding-top: 0; margin-bottom: 40px;">
                    <div class="wow fadeInUp" data-wow-delay="0.3s">
                        <div class="stat-card">
                            <span class="stat-num">{{ $statNum1 }}</span>
                            <span class="stat-label">{{ $statLbl1 }}</span>
                        </div>
                    </div>
                    <div class="wow fadeInUp" data-wow-delay="0.4s">
                        <div class="stat-card">
                            <span class="stat-num">{{ $statNum2 }}</span>
                            <span class="stat-label">{{ $statLbl2 }}</span>
                        </div>
                    </div>
                    <div class="wow fadeInUp" data-wow-delay="0.5s">
                        <div class="stat-card">
                            <span class="stat-num">{{ $statNum3 }}</span>
                            <span class="stat-label">{{ $statLbl3 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons (Moved below stats row) -->
                <div class="action-buttons wow fadeInUp" data-wow-delay="0.2s" style="margin-bottom: 0;">
                    <a href="{{ $btnLink1 }}" class="btn-about btn-filled">{{ $btnText1 }}</a>
                    <a href="{{ $btnLink2 }}" class="btn-about btn-outline">{{ $btnText2 }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Giới thiệu chung -->
    @php
        $aboutWidget = $widgets['about-us'] ?? null;
        $aboutCat = (isset($aboutWidget->object) && $aboutWidget->object->isNotEmpty()) ? $aboutWidget->object->first() : null;
        $aboutPost = ($aboutCat && $aboutCat->posts->isNotEmpty()) ? $aboutCat->posts->first() : null;
        
        $aboutTitle = 'GIỚI THIỆU VỀ CHÚNG TÔI';
        $aboutHighlight = 'VIREX là đơn vị chuyên cung cấp ống dẫn nước inox không hàn, ống PCCC thép carbon không hàn và phụ kiện công trình hiện đại, chất lượng hàng đầu. Chúng tôi mang đến giải pháp tối ưu cho mọi công trình của bạn.';
        $aboutContent = '<p>Với triết lý đặt chất lượng sản phẩm và sự an toàn của công trình lên hàng đầu, VIREX luôn nghiên cứu và tuyển chọn kỹ lưỡng các dòng sản phẩm có chất liệu bền bỉ, khả năng chịu lực cao và đạt chuẩn kiểm định nghiêm ngặt.</p><p>Sứ mệnh của chúng tôi là kiến tạo nên những công trình an toàn, bền bỉ và chất lượng cho mọi gia đình Việt, song hành cùng dịch vụ bảo hành và chăm sóc khách hàng chuyên nghiệp, tận tâm nhất.</p>';
        $aboutMainImage = '/vendor/frontend/img/project/tazen/project_1.png';
        $aboutSubImage = '/vendor/frontend/img/project/tazen/project_2.png';

        if ($aboutPost) {
            $postLang = $aboutPost->languages->first();
            if ($postLang) {
                $aboutTitle = $postLang->name;
                $aboutHighlight = strip_tags($postLang->description);
                $aboutContent = $postLang->content;
            }
            $aboutMainImage = $aboutPost->image;
            $album = json_decode($aboutPost->album, true);
            if (!empty($album) && is_array($album)) {
                $aboutSubImage = $album[0];
            }
        }
    @endphp

    <div class="about-intro-section">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-large uk-flex-middle" data-uk-grid-margin>
                <!-- Left text column -->
                <div class="uk-width-large-1-2 uk-width-1-1 text-col wow fadeInLeft">
                    <span class="sub-title">— Về chúng tôi</span>
                    <h2 class="section-title">{!! html_entity_decode($aboutTitle) !!}</h2>
                    <div class="text-content">
                        @if($aboutHighlight)
                            <p class="p-highlight">
                                {!! html_entity_decode($aboutHighlight) !!}
                            </p>
                        @endif
                        {!! html_entity_decode($aboutContent) !!}
                    </div>
                </div>

                <!-- Right image column -->
                <div class="uk-width-large-1-2 uk-width-1-1 image-col wow fadeInRight">
                    <div class="about-featured-image">
                        <img src="{{ $aboutMainImage }}" alt="{{ $aboutTitle }}">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Section 3: Năng lực kho bãi -->
    @php
        $warehouseTitle = $introduces['block_2_warehouse_title'] ?? 'Năng lực kho bãi';
        $warehouseDesc = $introduces['block_2_warehouse_desc'] ?? 'Diện tích 6000m2, sức chứa khoảng 500 tấn hàng hóa';
        
        $warehouseImages = [];
        for ($i = 1; $i <= 8; $i++) {
            $img = $introduces["block_2_warehouse_img_$i"] ?? '';
            if (!empty($img)) {
                $warehouseImages[] = $img;
            }
        }
        $totalImages = count($warehouseImages);
    @endphp

    <div class="about-warehouse-section">
        <div class="uk-container uk-container-center">
            <div class="section-header uk-text-center wow fadeInUp">
                <span class="sub-title">— NĂNG LỰC</span>
                <h2 class="section-title">{!! html_entity_decode($warehouseTitle) !!}</h2>
                @if($warehouseDesc)
                    <p class="section-desc">{{ $warehouseDesc }}</p>
                @endif
            </div>

            <div class="warehouse-grid uk-margin-large-top">
                <div class="uk-grid uk-grid-medium" data-uk-grid-margin>
                    @foreach($warehouseImages as $index => $imgUrl)
                        @php
                            $isHidden = $index >= 4;
                        @endphp
                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-1-1 warehouse-item {{ $isHidden ? 'hidden-image' : '' }} wow fadeInUp" 
                             data-wow-delay="{{ 0.1 * ($index % 4) }}s"
                             style="{{ $isHidden ? 'display: none;' : '' }}">
                            <a href="{{ $imgUrl }}" class="img-wrapper" data-uk-lightbox="{group:'warehouse'}">
                                <img src="{{ $imgUrl }}" alt="Kho bãi {{ $index + 1 }}">
                                @if($index === 3 && $totalImages > 4)
                                    <div class="more-overlay">
                                        <span>+{{ $totalImages - 4 }} ảnh khác</span>
                                    </div>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
                @if($totalImages > 4)
                    <div class="view-more-container uk-text-center uk-margin-large-top wow fadeInUp">
                        <button type="button" id="btn-toggle-warehouse" class="btn-toggle-warehouse">
                            Xem thêm hình ảnh <i class="fa fa-angle-down"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Section 4: Tại sao nên lựa chọn VIREX -->
    @php
        $whyTitle = $introduces['block_3_why_title'] ?? 'TẠI SAO NÊN LỰA CHỌN VIREX';
    @endphp

    <div class="about-why-section">
        <div class="uk-container uk-container-center">
            <div class="section-header uk-text-center wow fadeInUp">
                <span class="sub-title">— GIÁ TRỊ CỐT LÕI</span>
                <h2 class="section-title">{!! html_entity_decode($whyTitle) !!}</h2>
            </div>

            <div class="why-grid uk-grid uk-grid-large uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-width-1-1 uk-margin-large-top" data-uk-grid-margin>
                @for($i = 1; $i <= 4; $i++)
                    @php
                        $icon = $introduces["block_3_why_icon_$i"] ?? '';
                        $title = $introduces["block_3_why_title_$i"] ?? '';
                        $desc = $introduces["block_3_why_desc_$i"] ?? '';
                    @endphp
                    @if($title)
                        <div class="why-item-wrapper wow fadeInUp" data-wow-delay="{{ 0.1 * $i }}s">
                            <div class="why-card">
                                @if($icon)
                                    <div class="why-icon">
                                        <img src="{{ $icon }}" alt="{{ $title }}">
                                    </div>
                                @endif
                                <h3 class="why-card-title">{{ $title }}</h3>
                                <p class="why-card-desc">{{ $desc }}</p>
                            </div>
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    </div>


    <!-- Section 5: Khối liên hệ hỗ trợ (Solid green background) -->
    @php
        $contactTitle = $introduces['block_4_contact_title'] ?? 'Bạn đang cần giải pháp phù hợp?';
        $contactDesc = $introduces['block_4_contact_desc'] ?? 'Kết nối ngay với chuyên gia của chúng tôi để nhận giải pháp phù hợp.';
    @endphp

    <div class="about-contact-banner-section">
        <div class="uk-container uk-container-center">
            <div class="section-header uk-text-center wow fadeInUp">
                <h2 class="contact-title">{!! html_entity_decode($contactTitle) !!}</h2>
                @if($contactDesc)
                    <p class="contact-desc">{{ $contactDesc }}</p>
                @endif
            </div>

            <div class="contact-cards-grid uk-grid uk-grid-large uk-grid-width-large-1-3 uk-grid-width-1-1 uk-margin-large-top" data-uk-grid-margin>
                <!-- Box 1 -->
                @if(isset($introduces['block_4_contact_title_1']))
                    <div class="contact-card-item wow fadeInUp" data-wow-delay="0.1s">
                        <div class="contact-card">
                            <div class="card-icon-circle">
                                <i class="fa fa-phone"></i>
                            </div>
                            <h3 class="card-label">{{ $introduces['block_4_contact_title_1'] }}</h3>
                            <span class="card-value">{{ $introduces['block_4_contact_btn_text_1'] }}</span>
                            <a href="{{ $introduces['block_4_contact_link_1'] }}" class="btn-card-action">Gọi ngay</a>
                        </div>
                    </div>
                @endif

                <!-- Box 2 -->
                @if(isset($introduces['block_4_contact_title_2']))
                    <div class="contact-card-item wow fadeInUp" data-wow-delay="0.2s">
                        <div class="contact-card">
                            <div class="card-icon-circle">
                                <i class="fa fa-comments"></i>
                            </div>
                            <h3 class="card-label">{{ $introduces['block_4_contact_title_2'] }}</h3>
                            <span class="card-value">{{ $introduces['block_4_contact_btn_text_2'] }}</span>
                            <a href="{{ $introduces['block_4_contact_link_2'] }}" class="btn-card-action" target="_blank">Nhắn Zalo</a>
                        </div>
                    </div>
                @endif

                <!-- Box 3 -->
                @if(isset($introduces['block_4_contact_title_3']))
                    <div class="contact-card-item wow fadeInUp" data-wow-delay="0.3s">
                        <div class="contact-card">
                            <div class="card-icon-circle">
                                <i class="fa fa-paper-plane"></i>
                            </div>
                            <h3 class="card-label">{{ $introduces['block_4_contact_title_3'] }}</h3>
                            <span class="card-value">{{ $introduces['block_4_contact_btn_text_3'] }}</span>
                            <a href="{{ $introduces['block_4_contact_link_3'] }}" class="btn-card-action">Gửi yêu cầu</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Warehouse toggle hide/show remaining images
        const btnToggle = document.getElementById('btn-toggle-warehouse');
        if (btnToggle) {
            let isExpanded = false;
            btnToggle.addEventListener('click', function() {
                const hiddenItems = document.querySelectorAll('.warehouse-item.hidden-image');
                isExpanded = !isExpanded;
                
                hiddenItems.forEach(item => {
                    if (isExpanded) {
                        item.style.display = 'block';
                        // Trigger wow animations if not run yet
                        item.classList.add('wow', 'fadeInUp');
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                if (isExpanded) {
                    btnToggle.innerHTML = 'Ẩn bớt hình ảnh <i class="fa fa-angle-up"></i>';
                } else {
                    btnToggle.innerHTML = 'Xem thêm hình ảnh <i class="fa fa-angle-down"></i>';
                    // Scroll back to warehouse section top
                    document.querySelector('.about-warehouse-section').scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    });
</script>
@endsection
