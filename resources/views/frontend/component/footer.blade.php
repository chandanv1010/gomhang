<footer class="gomhang-footer">
    <div class="uk-container uk-container-center">
        <!-- Top Columns -->
        <div class="footer-top-row">
            <!-- Left: Showroom Hà Nội -->
            <div class="footer-col showroom-col">
                <h3>Showroom Hà Nội</h3>
                <p class="showroom-hours">Mở cửa: {{ $system['contact_showroom_hours'] ?? '8h-22h' }}</p>
                <ul class="showroom-list">
                    <li><i class="fa fa-map-marker"></i> {{ $system['contact_showroom_1'] ?? 'Số 83 Hàng Bông, Hoàn Kiếm: 0929.466.868' }}</li>
                </ul>
            </div>

            <!-- Middle-Left: Showroom TP. Hồ Chí Minh -->
            <div class="footer-col showroom-col">
                <h3>Showroom TP. Hồ Chí Minh</h3>
                <p class="showroom-hours">Mở cửa: 8h-22h</p>
                <ul class="showroom-list">
                    <li><i class="fa fa-map-marker"></i> 270 Hoàng Văn Thụ, Tân Bình: 0914.83.89.89</li>
                    <li><i class="fa fa-map-marker"></i> 1040 Cách Mạng Tháng 8, Tân Bình: 0919.89.19.00</li>
                    <li><i class="fa fa-map-marker"></i> Số 380 Đường 3/2, Quận 10: 0912.83.89.89</li>
                </ul>
            </div>

            <!-- Middle-Right: Điện thoại, liên hệ -->
            <div class="footer-col contact-col">
                <h3>Điện thoại, liên hệ</h3>
                <p class="contact-hours">Hoạt động: 8h-22h</p>
                <ul class="contact-phones-list">
                    <li><i class="fa fa-phone"></i> Gọi mua hàng: <a href="tel:{{ $system['contact_contact_buy'] ?? '0934439055' }}">{{ $system['contact_contact_buy'] ?? '093.443.9055' }}</a></li>
                    <li><i class="fa fa-phone"></i> Gọi tư vấn kỹ thuật: <a href="tel:{{ $system['contact_contact_support'] ?? '0934439055' }}">{{ $system['contact_contact_support'] ?? '093.443.9055' }}</a></li>
                    <li><i class="fa fa-phone"></i> Gọi bảo hành: <a href="tel:{{ $system['contact_contact_warranty'] ?? '0934439055' }}">{{ $system['contact_contact_warranty'] ?? '093.443.9055' }}</a></li>
                    <li><i class="fa fa-phone"></i> Hợp tác kinh doanh: <a href="tel:{{ $system['contact_contact_biz'] ?? '0934439055' }}">{{ $system['contact_contact_biz'] ?? '093.443.9055' }}</a></li>
                </ul>
            </div>

            <!-- Right: Hỗ trợ & Chính sách -->
            <div class="footer-col policies-col">
                <h3>Chính sách & Hỗ trợ</h3>
                <ul class="showroom-list">
                    <li><i class="fa fa-angle-right"></i> <a href="{{ $system['contact_footer_shipping_link'] ?? '#' }}">Chính sách vận chuyển</a></li>
                    <li><i class="fa fa-angle-right"></i> <a href="{{ $system['contact_footer_privacy_link'] ?? '#' }}">Chính sách bảo mật</a></li>
                    <li><i class="fa fa-angle-right"></i> <a href="{{ $system['contact_footer_warranty_link'] ?? '#' }}">Chính sách bảo hành</a></li>
                    <li><i class="fa fa-angle-right"></i> <a href="{{ $system['contact_footer_shop_link'] ?? '#' }}">Địa chỉ shop</a></li>
                </ul>
            </div>
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
            <div class="policies-info-box">
                <div class="policies-top-links">
                    <span>Hotline: {{ $system['contact_contact_buy'] ?? '' }}</span> – 
                    <span>Giao hàng toàn quốc, thanh toán sau khi kiểm tra</span> – 
                    <a href="{{ $system['contact_footer_shipping_link'] ?? '#' }}"><b>Chính sách vận chuyển</b></a> – 
                    <a href="{{ $system['contact_footer_shop_link'] ?? '#' }}"><b>Địa chỉ shop</b></a>
                </div>
                <div class="policies-bottom-links">
                    <a href="{{ $system['contact_footer_privacy_link'] ?? '#' }}">Chính sách bảo mật</a> | 
                    <a href="{{ $system['contact_footer_warranty_link'] ?? '#' }}">Chính sách bảo hành</a>
                    @if(!empty($system['contact_footer_license']))
                        | <span>{{ $system['contact_footer_license'] }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Row: Copyright -->
        <div class="footer-bottom-row uk-text-center">
            <p class="copyright-text">{{ $system['contact_footer_copyright'] ?? 'Bản quyền thuộc về Gomhang.vn 2012-2024' }}</p>
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
.footer-col h3 {
    color: #ffffff;
    font-size: 15px;
    font-weight: bold;
    margin: 0 0 10px 0;
    text-transform: none;
}
.showroom-hours,
.contact-hours {
    font-size: 12px;
    color: #888888;
    margin: 0 0 12px 0;
}
.showroom-list,
.contact-phones-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.showroom-list li,
.contact-phones-list li {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.showroom-list li i {
    color: #d61c00;
    font-size: 14px;
}
.contact-phones-list li i {
    color: #d61c00;
    font-size: 12px;
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
