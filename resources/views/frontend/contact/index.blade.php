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

    @php
        $address = trim((string) ($system['contact_address'] ?? $system['contact_office'] ?? ''));
        $hotlineDisplay = system_phone($system ?? null);
        $hotlineDigits = system_phone($system ?? null, true);
        $email = trim((string) ($system['contact_email'] ?? ''));

        $mapUrl = trim((string) ($system['contact_map'] ?? ''));
        if ($mapUrl !== '' && str_contains($mapUrl, 'embed')) {
            $embedMap = $mapUrl;
        } else {
            $embedMap = 'https://maps.google.com/maps?q=' . urlencode($address !== '' ? $address : 'Hà Nội') . '&output=embed';
        }
    @endphp

    <section class="contact-page-wrapper">
        <div class="uk-container uk-container-center">

            {{-- Chỉ có một địa chỉ nên khối này căn giữa, thay vì dàn 3 cột như mẫu --}}
            <div class="office-row">
                <div class="office-card">
                    <h2 class="office-title">Văn phòng:</h2>
                    <ul class="office-list uk-list">
                        @if($address !== '')
                            <li>
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                <span><strong>Địa chỉ:</strong> {{ $address }}</span>
                            </li>
                        @endif
                        @if($hotlineDigits !== '')
                            <li>
                                <i class="fa fa-phone" aria-hidden="true"></i>
                                <span><strong>Hotline:</strong>
                                    <a href="tel:{{ $hotlineDigits }}">{{ $hotlineDisplay }}</a>
                                </span>
                            </li>
                        @endif
                        @if($email !== '')
                            <li>
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                <span><strong>Email:</strong>
                                    <a href="mailto:{{ $email }}">{{ $email }}</a>
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Form bên trái, bản đồ bên phải --}}
            <div class="contact-main uk-grid uk-grid-medium" data-uk-grid-margin>
                <div class="uk-width-large-1-2 uk-width-medium-1-1">
                    <h2 class="contact-section-title">Gửi thông tin đến chúng tôi</h2>

                    <form class="contact-form" id="contactForm" method="post" novalidate>
                        @csrf
                        <div class="form-alert" id="contactAlert" role="alert" hidden></div>

                        <div class="field-row">
                            <div class="field">
                                <input type="text" name="name" class="field-input" placeholder="Họ tên *" required>
                            </div>
                            <div class="field">
                                <input type="tel" name="phone" class="field-input" placeholder="Điện thoại *" required>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field">
                                <input type="email" name="email" class="field-input" placeholder="Email *" required>
                            </div>
                            <div class="field">
                                <input type="text" name="address" class="field-input" placeholder="Địa chỉ">
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field field-full">
                                <textarea name="message" rows="5" class="field-textarea"
                                          placeholder="Nhập nội dung bạn muốn liên hệ"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn-send">
                            <span>Gửi đi</span>
                            <span class="btn-send__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"
                                     focusable="false">
                                    <path d="M4.5 12h14" />
                                    <path d="M12.5 6l6 6-6 6" />
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>

                <div class="uk-width-large-1-2 uk-width-medium-1-1">
                    <h2 class="contact-section-title">Bản đồ</h2>

                    <div class="map-embed">
                        <iframe
                            src="{{ $embedMap }}"
                            width="100%"
                            height="360"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            title="Bản đồ đường tới văn phòng"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <div class="working-hours">
                        <h3 class="hours-title"><i class="fa fa-clock-o"></i> Giờ làm việc</h3>
                        <ul class="hours-list uk-list">
                            <li><span class="day">Thứ 2 – Thứ 6:</span> <span class="time">8:00 – 18:00</span></li>
                            <li><span class="day">Thứ 7:</span> <span class="time">8:00 – 12:00</span></li>
                            <li><span class="day">Chủ nhật:</span> <span class="time closed">Nghỉ</span></li>
                        </ul>
                    </div>

                    <div class="contact-social-row">
                        <a href="{{ $system['contact_facebook'] ?? '#' }}" target="_blank" rel="noopener"
                           class="social-contact-btn facebook">
                            <i class="fa fa-facebook"></i> Facebook
                        </a>
                        @if($hotlineDigits !== '')
                            <a href="tel:{{ $hotlineDigits }}" class="social-contact-btn phone">
                                <i class="fa fa-phone"></i> Gọi ngay
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>

    <style>
        .contact-page-wrapper {
            padding: 50px 0 80px;
            background: #ffffff;
        }

        /* ---- Khối địa chỉ (căn giữa vì chỉ có 1 văn phòng) ---- */
        .office-row {
            display: flex;
            justify-content: center;
            margin-bottom: 55px;
        }

        .office-card {
            width: 100%;
            max-width: 620px;
            background: linear-gradient(135deg, #f7f7f8 0%, #eeeff1 100%);
            border-radius: 6px;
            padding: 26px 30px;
            border-left: 4px solid #e01b24;
        }

        .office-title {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a1a;
            text-transform: uppercase;
            margin: 0 0 16px;
        }

        .office-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .office-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 5px 0;
            font-size: 14px;
            color: #444;
            line-height: 1.6;
        }

        .office-list .fa {
            color: #e01b24;
            font-size: 15px;
            width: 16px;
            text-align: center;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .office-list strong {
            color: #1a1a1a;
            font-weight: 700;
        }

        .office-list a {
            color: #e01b24;
            text-decoration: none;
            font-weight: 600;
        }

        .office-list a:hover {
            color: #1a1a1a;
        }

        /* ---- Tiêu đề 2 khối dưới ---- */
        .contact-section-title {
            font-size: 22px;
            font-weight: 800;
            color: #1a1a1a;
            text-transform: uppercase;
            margin: 0 0 22px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ececec;
            position: relative;
        }

        .contact-section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 64px;
            height: 2px;
            background: #e01b24;
        }

        /* ---- Form ---- */
        .field-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        .field {
            flex: 1;
            min-width: 0;
        }

        .field-input,
        .field-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #dcdfe3;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            font-family: inherit;
            background: #fff;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .field-input:focus,
        .field-textarea:focus {
            border-color: #e01b24;
        }

        .field-input.has-error,
        .field-textarea.has-error {
            border-color: #e01b24;
            background: #fff6f5;
        }

        .field-textarea {
            resize: vertical;
        }

        .btn-send {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            height: 50px;
            padding: 0 8px 0 30px;
            background: #e01b24;
            color: #fff;
            border: none;
            border-radius: 999px;
            /* Nút vốn không thừa hưởng font của trang, phải khai báo lại */
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(224, 27, 36, 0.26);
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
        }

        .btn-send__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            transition: transform 0.25s ease, background-color 0.25s ease;
        }

        /* Mũi tên vẽ bằng SVG thay vì icon font, để không phụ thuộc việc
           font icon có tải được hay không. */
        .btn-send__icon svg {
            display: block;
            width: 16px;
            height: 16px;
        }

        .btn-send:hover {
            background: #c2151d;
            box-shadow: 0 6px 18px rgba(224, 27, 36, 0.34);
        }

        .btn-send:hover .btn-send__icon {
            background: rgba(255, 255, 255, 0.28);
            transform: translateX(3px);
        }

        .btn-send:active {
            box-shadow: 0 1px 5px rgba(224, 27, 36, 0.3);
        }

        .btn-send:focus-visible {
            outline: 2px solid #1a1a1a;
            outline-offset: 3px;
        }

        .btn-send[disabled] {
            background: #d98a8a;
            cursor: not-allowed;
            box-shadow: none;
        }

        .btn-send[disabled]:hover .btn-send__icon {
            transform: none;
            background: rgba(255, 255, 255, 0.18);
        }

        .form-alert {
            padding: 11px 15px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 13px;
            border-left: 4px solid transparent;
        }

        .form-alert.is-error {
            background: #fdecea;
            border-left-color: #e01b24;
            color: #a4141b;
        }

        .form-alert.is-success {
            background: #eef9f1;
            border-left-color: #27ae60;
            color: #1e8449;
        }

        /* ---- Bản đồ ---- */
        .map-embed {
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e6e8eb;
            line-height: 0;
        }

        /* ---- Giờ làm việc ---- */
        .working-hours {
            margin-top: 24px;
        }

        .hours-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .hours-title .fa {
            color: #e01b24;
            margin-right: 6px;
        }

        .hours-list li {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px dashed #e8e8e8;
            font-size: 14px;
        }

        .hours-list .day { color: #444; font-weight: 500; }
        .hours-list .time { color: #1a1a1a; font-weight: 600; }
        .hours-list .closed { color: #e01b24; }

        /* ---- Social ---- */
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
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none !important;
            transition: background 0.25s ease;
        }

        .social-contact-btn.facebook {
            background: #1877f2;
            color: #fff !important;
        }

        .social-contact-btn.facebook:hover {
            background: #1456b5;
        }

        .social-contact-btn.phone {
            background: linear-gradient(135deg, #e01b24 0%, #a4141b 100%);
            color: #fff !important;
        }

        .social-contact-btn.phone:hover {
            background: #1a1a1a;
        }

        /* ---- Responsive ---- */
        @media (max-width: 960px) {
            .contact-section-title { font-size: 19px; }
            .office-card { padding: 22px 20px; }
        }

        @media (max-width: 640px) {
            .contact-page-wrapper { padding: 35px 0 55px; }
            .field-row { flex-direction: column; gap: 14px; }
            .contact-social-row { flex-direction: column; }
        }
    </style>

    <script>
        (function () {
            var form = document.getElementById('contactForm');
            if (!form) return;

            var alertBox = document.getElementById('contactAlert');
            var button = form.querySelector('.btn-send');

            function showAlert(message, kind) {
                alertBox.textContent = message;
                alertBox.className = 'form-alert is-' + kind;
                alertBox.hidden = false;
            }

            function markErrors(names) {
                form.querySelectorAll('.has-error').forEach(function (el) {
                    el.classList.remove('has-error');
                });
                names.forEach(function (name) {
                    var field = form.querySelector('[name="' + name + '"]');
                    if (field) field.classList.add('has-error');
                });
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                var data = new FormData(form);
                var missing = ['name', 'phone', 'email'].filter(function (name) {
                    return !String(data.get(name) || '').trim();
                });

                if (missing.length) {
                    markErrors(missing);
                    showAlert('Vui lòng nhập đầy đủ họ tên, điện thoại và email.', 'error');
                    return;
                }

                markErrors([]);
                button.disabled = true;

                fetch('{{ route('ajax.contact.advise') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                    .then(function (response) { return response.json(); })
                    .then(function (result) {
                        // Endpoint trả 422 kèm messages theo từng field khi validate lỗi.
                        if (result.status === 422) {
                            var fields = Object.keys(result.messages || {}).filter(function (key) {
                                return result.messages[key];
                            });
                            markErrors(fields);
                            showAlert(fields.map(function (key) {
                                return result.messages[key];
                            }).join(' ') || 'Thông tin chưa hợp lệ.', 'error');
                            return;
                        }

                        if (result.code === 10) {
                            form.reset();
                            showAlert('Gửi thông tin thành công, chúng tôi sẽ liên hệ lại với bạn sớm nhất.', 'success');
                            return;
                        }

                        showAlert('Có lỗi xảy ra, bạn vui lòng thử lại.', 'error');
                    })
                    .catch(function () {
                        showAlert('Không gửi được thông tin, bạn vui lòng thử lại.', 'error');
                    })
                    .finally(function () {
                        button.disabled = false;
                    });
            });
        })();
    </script>

@endsection
