<div class="profile-sidebar">
    <h3 class="sidebar-title">Tài khoản</h3>
    <ul class="sidebar-menu">
        <li><a href="{{ route('customer.account') }}"><i class="fa fa-user"></i> Thông tin cá nhân</a></li>
        <li><a href="{{ route('customer.password') }}"><i class="fa fa-lock"></i> Thay đổi mật khẩu</a></li>
        <li><a href="{{ route('customer.order') }}"><i class="fa fa-file-text"></i> Đơn hàng đã mua</a></li>
        <li><a href="{{ route('customer.point.history') }}"><i class="fa fa-history"></i> Lịch sử giao dịch</a></li>
        <li><a href="{{ route('product.compare.index') }}"><i class="fa fa-columns"></i> So sánh sản phẩm</a></li>
        <li>
            <button class="logout-btn">
                <i class="fa fa-sign-out"></i> Đăng xuất
            </button>
        </li>
    </ul>
</div>
