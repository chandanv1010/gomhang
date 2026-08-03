@extends('frontend.homepage.layout')
@section('content')

    {{-- Breadcrumb Hero --}}
    <div class="cat-hero-section" style="background-image: url('/vendor/frontend/img/project/breadcrumb.png');">
        <div class="cat-hero-overlay"></div>
        <div class="cat-hero-shapes">
            <div class="shape shape-left"></div>
            <div class="shape shape-right"></div>
        </div>
        <div class="uk-container uk-container-center cat-hero-container">
            <h1 class="cat-hero-title">Liên Hệ</h1>
            <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs">
                <li><a href="/">Trang chủ</a></li>
                <li class="separator">&raquo;</li>
                <li><a href="#" onclick="return false;">Liên hệ</a></li>
            </ul>
        </div>
    </div>

    <section class="contact-page-wrapper">
        <div class="uk-container uk-container-center">

            {{-- Contact info cards row --}}
            <div class="contact-cards-row uk-grid uk-grid-medium" data-uk-grid-margin>
                <div class="uk-width-large-1-3 uk-width-medium-1-1 uk-width-1-1">
                    <div class="contact-card">
                        <div class="card-icon">
                            <i class="fa fa-map-marker"></i>
                        </div>
                        <h3 class="card-title">Địa chỉ</h3>
                        <p class="card-value">{{ $system['contact_address'] ?? 'Hà Nội, Việt Nam' }}</p>
                    </div>
                </div>
                <div class="uk-width-large-1-3 uk-width-medium-1-2 uk-width-1-1">
                    <div class="contact-card">
                        <div class="card-icon">
                            <i class="fa fa-phone"></i>
                        </div>
                        <h3 class="card-title">Hotline / Zalo</h3>
                        <p class="card-value">
                            <a href="tel:{{ $system['contact_hotline'] ?? '' }}">{{ $system['contact_hotline'] ?? '' }}</a>
                        </p>
                    </div>
                </div>
                <div class="uk-width-large-1-3 uk-width-medium-1-2 uk-width-1-1">
                    <div class="contact-card">
                        <div class="card-icon">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <h3 class="card-title">Email</h3>
                        <p class="card-value">
                            <a href="mailto:{{ $system['contact_email'] ?? '' }}">{{ $system['contact_email'] ?? '' }}</a>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main contact section: Map & Info --}}
            <div class="contact-main uk-grid uk-flex uk-flex-center" data-uk-grid-margin>
                {{-- Map + Extra Info --}}
                <div class="uk-width-large-2-3 uk-width-medium-1-1">
                    <div class="contact-map-box">
                        @php
                            $mapUrl = $system['contact_map'] ?? '';
                            // Convert Google Maps URL to embed URL if needed
                            $embedMap = '';
                            if (!empty($mapUrl)) {
                                if (strpos($mapUrl, 'embed') !== false) {
                                    $embedMap = $mapUrl;
                                } else {
                                    $embedMap = 'https://maps.google.com/maps?q=' . urlencode($system['contact_address'] ?? 'Hà Nội') . '&output=embed';
                                }
                            } else {
                                $embedMap = 'https://maps.google.com/maps?q=' . urlencode($system['contact_address'] ?? '116 Thái Hà, Hà Nội') . '&output=embed';
                            }
                        @endphp
                        <div class="map-embed">
                            <iframe 
                                src="{{ $embedMap }}"
                                width="100%" 
                                height="380" 
                                style="border:0; border-radius: 12px;" 
                                allowfullscreen="" 
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                        <div class="working-hours">
                            <h3 class="hours-title"><i class="fa fa-clock-o"></i> Giờ làm việc</h3>
                            <ul class="hours-list uk-list">
                                <li>
                                    <span class="day">Thứ 2 – Thứ 6:</span>
                                    <span class="time">8:00 – 18:00</span>
                                </li>
                                <li>
                                    <span class="day">Thứ 7:</span>
                                    <span class="time">8:00 – 12:00</span>
                                </li>
                                <li>
                                    <span class="day">Chủ nhật:</span>
                                    <span class="time closed">Nghỉ</span>
                                </li>
                            </ul>
                        </div>

                        <div class="contact-social-row">
                            <a href="{{ $system['contact_facebook'] ?? '#' }}" target="_blank" class="social-contact-btn facebook">
                                <i class="fa fa-facebook"></i> Facebook
                            </a>
                            <a href="tel:{{ $system['contact_hotline'] ?? '' }}" class="social-contact-btn phone">
                                <i class="fa fa-phone"></i> Gọi ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <style>
        .contact-page-wrapper {
            padding: 60px 0 80px;
            background: #f7f8fc;
        }

        /* Contact Cards */
        .contact-cards-row {
            margin-bottom: 50px;
        }

        .contact-card {
            background: #fff;
            border-radius: 14px;
            padding: 30px 24px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid #f27a24;
            height: 100%;
        }

        .contact-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }

        .contact-card .card-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #1e4794, #2557b0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .contact-card .card-icon .fa {
            font-size: 24px;
            color: #fff;
        }

        .contact-card .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e4794;
            margin-bottom: 10px;
        }

        .contact-card .card-value {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        .contact-card .card-value a {
            color: #f27a24;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-card .card-value a:hover {
            color: #1e4794;
        }

        /* Form box */
        .contact-form-box {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        }

        .form-tag {
            color: #f27a24;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .form-title {
            font-size: 26px;
            font-weight: 800;
            color: #1e4794;
            margin: 8px 0 10px;
        }

        .form-desc {
            color: #666;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .field-group {
            margin-bottom: 18px;
        }

        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #444;
            margin-bottom: 6px;
        }

        .field-label .required {
            color: #f27a24;
        }

        .field-input,
        .field-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e8ecf0;
            border-radius: 8px;
            font-size: 14px;
            color: #333;
            transition: border-color 0.3s;
            outline: none;
            background: #fafbfc;
            box-sizing: border-box;
        }

        .field-input:focus,
        .field-textarea:focus {
            border-color: #1e4794;
            background: #fff;
        }

        .field-textarea {
            resize: vertical;
        }

        .btn-contact-submit {
            background: linear-gradient(135deg, #f27a24, #e66b15);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 14px 36px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .btn-contact-submit:hover {
            background: linear-gradient(135deg, #1e4794, #2557b0);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30,71,148,0.3);
        }

        .form-error-box {
            background: #fee;
            border-left: 4px solid #e74c3c;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            color: #c0392b;
            font-size: 13px;
        }

        .form-success-box {
            background: #efffef;
            border-left: 4px solid #27ae60;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            color: #27ae60;
            font-size: 13px;
        }

        /* Map box */
        .contact-map-box {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.08);
        }

        .map-embed {
            margin-bottom: 24px;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Working hours */
        .working-hours {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-bottom: 20px;
        }

        .hours-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e4794;
            margin-bottom: 14px;
        }

        .hours-title .fa {
            color: #f27a24;
            margin-right: 6px;
        }

        .hours-list li {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px dashed #eee;
            font-size: 14px;
        }

        .hours-list .day { color: #444; font-weight: 500; }
        .hours-list .time { color: #1e4794; font-weight: 600; }
        .hours-list .closed { color: #e74c3c; }

        /* Social row */
        .contact-social-row {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .social-contact-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.3s ease;
        }

        .social-contact-btn.facebook {
            background: #1877f2;
            color: #fff !important;
        }

        .social-contact-btn.facebook:hover {
            background: #1456b5;
            color: #fff !important;
        }

        .social-contact-btn.phone {
            background: linear-gradient(135deg, #f27a24, #e66b15);
            color: #fff !important;
        }

        .social-contact-btn.phone:hover {
            background: linear-gradient(135deg, #1e4794, #2557b0);
            color: #fff !important;
        }

        /* Responsive */
        @media (max-width: 960px) {
            .contact-form-box,
            .contact-map-box {
                padding: 24px 20px;
            }

            .form-title {
                font-size: 22px;
            }
        }

        @media (max-width: 640px) {
            .contact-page-wrapper {
                padding: 40px 0 60px;
            }

            .contact-card {
                padding: 20px 16px;
            }

            .contact-social-row {
                flex-direction: column;
            }
        }
    </style>

@endsection
