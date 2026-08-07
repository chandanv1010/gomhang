<footer class="gomhang-footer">
    <div class="uk-container uk-container-center">
        <!-- Top Columns -->
        <div class="footer-top-row">
            {{-- Cột ngoài cùng bên trái gom TẤT CẢ thông tin liên hệ: địa chỉ,
                 điện thoại, email, website. Toàn bộ lấy từ Admin -> Cấu hình ->
                 Thông tin liên hệ, không có giá trị mặc định ghi cứng: xoá trong
                 admin là mất khỏi footer, chứ không rơi về số cũ. --}}
            @php
                $diaChi = array_values(array_filter([
                    $system['contact_showroom_1'] ?? null,
                    $system['contact_showroom_2'] ?? null,
                    $system['contact_showroom_3'] ?? null,
                ], fn ($line) => trim((string) $line) !== ''));

                $dienThoai = array_values(array_filter([
                    ['nhan' => 'Gọi mua hàng', 'so' => $system['contact_contact_buy'] ?? null],
                    ['nhan' => 'Gọi tư vấn kỹ thuật', 'so' => $system['contact_contact_support'] ?? null],
                    ['nhan' => 'Gọi bảo hành', 'so' => $system['contact_contact_warranty'] ?? null],
                    ['nhan' => 'Hợp tác kinh doanh', 'so' => $system['contact_contact_biz'] ?? null],
                ], fn ($d) => trim((string) $d['so']) !== ''));

                $email = trim((string) ($system['contact_email'] ?? ''));
                $website = trim((string) ($system['homepage_website'] ?? ''));
            @endphp

            <div class="footer-col contact-info-col">
                <h2>Thông tin liên hệ</h2>

                @if(count($diaChi))
                    <ul class="footer-info-list">
                        @foreach($diaChi as $line)
                            <li><i class="fa fa-map-marker"></i> {{ $line }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(count($dienThoai))
                    <ul class="footer-info-list">
                        @foreach($dienThoai as $d)
                            <li>
                                <i class="fa fa-phone"></i> {{ $d['nhan'] }}:
                                <a href="tel:{{ preg_replace('#[^0-9+]#', '', $d['so']) }}">{{ $d['so'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($email !== '' || $website !== '')
                    <ul class="footer-info-list">
                        @if($email !== '')
                            <li><i class="fa fa-envelope-o"></i> <a href="mailto:{{ $email }}">{{ $email }}</a></li>
                        @endif
                        @if($website !== '')
                            <li>
                                <i class="fa fa-globe"></i>
                                <a href="{{ $website }}" target="_blank" rel="noopener">{{ system_website_label($system ?? null) }}</a>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>

            {{-- Các cột bên phải là menu chân trang trong admin: mỗi mục cấp 1 là
                 một cột, các mục con là link. Thêm bớt cột ngay trong
                 Admin -> Menu -> Menu chân trang. --}}
            @foreach($menu['footer-menu'] ?? [] as $cot)
                @php
                    $cotLang = $cot['item']->languages->first();
                    $cotCon = $cot['children'] ?? [];
                @endphp
                @if($cotLang && $cotLang->pivot && count($cotCon))
                    <div class="footer-col footer-menu-col">
                        <h2>{{ $cotLang->pivot->name }}</h2>
                        <ul class="footer-info-list">
                            @foreach($cotCon as $link)
                                @php $linkLang = $link['item']->languages->first(); @endphp
                                @if($linkLang && $linkLang->pivot)
                                    <li>
                                        <i class="fa fa-angle-right"></i>
                                        <a href="{{ write_url($linkLang->pivot->canonical) }}">{{ $linkLang->pivot->name }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Middle Row: BCT Red Logo and Policies link -->
        <div class="footer-mid-row">
            @if(!empty($system['contact_footer_bct_logo']))
                <div class="bct-logo-box">
                    <a href="{{ $system['contact_footer_bct_link'] ?? '#' }}" target="_blank">
                        <img src="{{ $system['contact_footer_bct_logo'] }}" alt="Đăng ký Bộ Công Thương" class="bct-logo-img">
                    </a>
                </div>
            @endif
            {{-- Dòng giữa trước đây ghi cứng câu cam kết và cả nhãn của 4 link
                 chính sách, dù mấy link đó đã có ở cột bên phải. Giờ chỉ còn
                 hotline, câu cam kết và giấy phép - đều lấy từ admin, thiếu cái
                 nào thì ẩn cái đó. --}}
            @php
                $hotline = trim((string) ($system['contact_hotline'] ?? $system['contact_contact_buy'] ?? ''));
                $tagline = trim((string) ($system['contact_footer_tagline'] ?? ''));
                $license = trim((string) ($system['contact_footer_license'] ?? ''));
            @endphp
            @if($hotline !== '' || $tagline !== '' || $license !== '')
                <div class="policies-info-box">
                    @if($hotline !== '' || $tagline !== '')
                        <div class="policies-top-links">
                            @if($hotline !== '')
                                <span>Hotline:
                                    <a href="tel:{{ preg_replace('#[^0-9+]#', '', $hotline) }}">{{ $hotline }}</a>
                                </span>
                            @endif
                            @if($hotline !== '' && $tagline !== '') &ndash; @endif
                            @if($tagline !== '')
                                <span>{{ $tagline }}</span>
                            @endif
                        </div>
                    @endif
                    @if($license !== '')
                        <div class="policies-bottom-links">
                            <span>{{ $license }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Bottom Row: Copyright -->
        <div class="footer-bottom-row uk-text-center">
            {{-- Không đặt bản quyền mặc định ghi cứng: xoá trong admin thì phải
                 mất hẳn, chứ không rơi về tên và năm cũ. --}}
            @if(!empty($system['contact_footer_copyright']))
                <p class="copyright-text">{{ $system['contact_footer_copyright'] }}</p>
            @endif
        </div>
    </div>
</footer>

<style>
/* CSS layout to match Image 2 footer styling */
.gomhang-footer {
    background-color: #1c1c1c;
    color: #aeaeae;
    padding: 30px 0 20px 0;
    font-family: 'Manrope', sans-serif;
    font-size: 13px;
    border-top: 1px solid #333333;
}
.gomhang-footer a {
    color: #aeaeae !important;
    text-decoration: none;
    transition: color 0.2s;
}
.gomhang-footer a:hover {
    color: #ffffff !important;
}

/* Top columns layout */
.footer-top-row {
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #2d2d2d;
    padding-bottom: 25px;
    margin-bottom: 20px;
    gap: 40px;
}
.footer-col {
    flex: 1;
}
.footer-col h2,
.footer-col h3 {
    color: #ffffff;
    font-size: 15px;
    font-weight: bold;
    margin: 0 0 10px 0;
    text-transform: none;
}
/* Cột thông tin liên hệ gom nhiều nhóm (địa chỉ / điện thoại / email) nên
   giãn cách giữa các nhóm, còn trong nhóm thì các dòng sát nhau. */
.footer-info-list {
    list-style: none;
    padding: 0;
    margin: 0 0 14px 0;
}
.footer-info-list:last-child {
    margin-bottom: 0;
}
.footer-info-list li {
    margin-bottom: 8px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.5;
}
.footer-info-list li i {
    color: #e01b24;
    font-size: 13px;
    margin-top: 3px;
    flex-shrink: 0;
}
/* Cột thông tin rộng hơn cột menu vì chứa địa chỉ dài. */
.contact-info-col {
    flex: 1.6;
}
.footer-menu-col {
    flex: 1;
}

/* Middle row layout (BCT logo + policies info) */
.footer-mid-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #2d2d2d;
    margin-bottom: 15px;
}
.bct-logo-box {
    flex-shrink: 0;
}
.bct-logo-img {
    height: 48px;
    width: auto;
    object-fit: contain;
    display: block;
}
.policies-info-box {
    color: #aeaeae;
    line-height: 1.6;
}
.policies-top-links {
    font-size: 13px;
    margin-bottom: 4px;
}
.policies-top-links b {
    color: #ffffff;
    text-decoration: underline;
}
.policies-bottom-links {
    font-size: 12px;
    color: #888888;
}

/* Bottom copyright row */
.copyright-text {
    font-size: 11px;
    color: #666666;
    margin: 10px 0 0 0;
}

@media (max-width: 767px) {
    .footer-top-row {
        flex-direction: column;
        gap: 25px;
    }
    .footer-mid-row {
        flex-direction: column;
        text-align: center;
    }
}
</style>
