@extends('frontend.homepage.layout')

@section('content')
    <div class="profile-wrapper cat-bg">
        <div class="uk-container uk-container-center">

            {{-- HEADER PROFILE --}}
            @include('frontend.auth.customer.components.header')

            <div class="uk-grid uk-grid-medium mt30">

                {{-- SIDEBAR --}}
                <div class="uk-width-large-1-4">
                    @include('frontend.auth.customer.components.sidebar')
                </div>

                {{-- MAIN CONTENT --}}
                <div class="uk-width-large-3-4">
                    <div class="panel-profile">
                        <div class="panel-head">
                            <h2 class="heading-2"><span>Chi tiết đơn hàng #{{ $order->code }}</span></h2>
                            <div class="description">
                                Thông tin chi tiết về đơn hàng của bạn
                            </div>
                        </div>

                        <div class="panel-body">
                            @if (session('success'))
                                <div class="uk-alert uk-alert-success" data-uk-alert>
                                    <a href="" class="uk-alert-close uk-close"></a>
                                    <p>{{ session('success') }}</p>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="uk-alert uk-alert-danger" data-uk-alert>
                                    <a href="" class="uk-alert-close uk-close"></a>
                                    <p>{{ session('error') }}</p>
                                </div>
                            @endif

                            <div class="order-detail-wrapper">
                                {{-- Thông tin đơn hàng --}}
                                <div class="uk-panel  uk-margin-bottom" style="background: #f8f9fa;">
                                    <h3 class="uk-panel-title uk-text-bold uk-margin-bottom"
                                        style="font-size: 20px; padding: 12px 15px; margin: 0; background: #e8f4f0;">
                                        Thông tin đơn hàng</h3>
                                    <div class="uk-grid uk-grid-small" style="background: #fff; padding: 15px;">
                                        <div class="uk-width-medium-1-2">
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Mã đơn hàng:</span>
                                                <span class="uk-text-primary"
                                                    style="font-size: 15px;"><strong>#{{ $order->code }}</strong></span>
                                            </div>
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Ngày đặt:</span>
                                                <span
                                                    style="font-size: 15px;">{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
                                            </div>
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Cập nhật:</span>
                                                <span
                                                    style="font-size: 15px;">{{ date('d/m/Y H:i', strtotime($order->updated_at)) }}</span>
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-1-2">
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Trạng thái:</span>
                                                <span
                                                    class="uk-badge uk-badge-{{ $order->confirm == 'confirm' ? 'success' : ($order->confirm == 'cancle' ? 'danger' : 'warning') }}"
                                                    style="font-size: 16px; padding: 6px 12px;">
                                                    {{ __('order.confirm')[$order->confirm] ?? $order->confirm }}
                                                </span>
                                            </div>
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Thanh toán:</span>
                                                <span
                                                    class="uk-badge uk-badge-{{ $order->payment == 'paid' ? 'success' : 'warning' }}"
                                                    style="font-size: 16px; padding: 6px 12px;">
                                                    {{ $order->payment == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                                </span>
                                            </div>
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Giao hàng:</span>
                                                <span
                                                    class="uk-badge uk-badge-{{ $order->delivery == 'success' ? 'success' : ($order->delivery == 'processing' ? 'primary' : 'warning') }}"
                                                    style="font-size: 16px; padding: 6px 12px;">
                                                    {{ $order->delivery == 'pending'
                                                        ? 'Chờ giao hàng'
                                                        : ($order->delivery == 'processing'
                                                            ? 'Đang giao hàng'
                                                            : ($order->delivery == 'success'
                                                                ? 'Đã giao hàng'
                                                                : '-')) }}
                                                </span>


                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Thông tin người nhận --}}
                                <div class="uk-panel  uk-margin-bottom" style="background: #f8f9fa;">
                                    <h3 class="uk-panel-title uk-text-bold uk-margin-bottom"
                                        style="font-size: 20px; padding: 12px 15px; margin: 0; background: #e8f4f0;">
                                        Thông tin người nhận</h3>
                                    <div class="uk-grid uk-grid-small" style="background: #fff; padding: 15px;">
                                        <div class="uk-width-medium-1-2">
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Họ tên:</span>
                                                <span style="font-size: 15px;">{{ $order->fullname }}</span>
                                            </div>
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Số điện thoại:</span>
                                                <span style="font-size: 15px;">{{ $order->phone }}</span>
                                            </div>
                                            @if ($order->email)
                                                <div class="uk-margin-small" style="padding: 8px 0;">
                                                    <span class="uk-text-bold uk-display-inline-block"
                                                        style="min-width: 140px; font-size: 15px;">Email:</span>
                                                    <span style="font-size: 15px;">{{ $order->email }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="uk-width-medium-1-2">
                                            <div class="uk-margin-small" style="padding: 8px 0;">
                                                <span class="uk-text-bold uk-display-inline-block"
                                                    style="min-width: 140px; font-size: 15px;">Địa chỉ:</span>
                                                <span style="font-size: 15px;">{{ $order->address }}</span>
                                            </div>
                                            @if ($order->province_name || $order->district_name || $order->ward_name)
                                                <div class="uk-margin-small" style="padding: 8px 0;">
                                                    <span class="uk-text-bold uk-display-inline-block"
                                                        style="min-width: 140px; font-size: 15px;">Tỉnh/TP - Quận/Huyện -
                                                        Phường/Xã:</span>
                                                    <span style="font-size: 15px;">
                                                        {{ $order->ward_name ?? '' }}{{ $order->ward_name && ($order->district_name || $order->province_name) ? ', ' : '' }}
                                                        {{ $order->district_name ?? '' }}{{ $order->district_name && $order->province_name ? ', ' : '' }}
                                                        {{ $order->province_name ?? '' }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($order->description)
                                                <div class="uk-margin-small" style="padding: 8px 0;">
                                                    <span class="uk-text-bold uk-display-inline-block"
                                                        style="min-width: 140px; font-size: 15px;">Ghi chú:</span>
                                                    <span style="font-size: 15px;">{{ $order->description }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Phương thức thanh toán --}}
                                <div class="uk-panel  uk-margin-bottom" style="background: #f8f9fa;">
                                    <h3 class="uk-panel-title uk-text-bold uk-margin-bottom uk-margin-2"
                                        style="font-size: 20px; padding: 12px 15px; margin: 0; background: #e8f4f0;">
                                        Phương thức thanh toán</h3>
                                    <div class="uk-flex uk-flex-middle" style="background: #fff; padding: 15px;">
                                        @php
                                            $paymentMethods = __('payment.method');
                                            $methodInfo = collect($paymentMethods)->firstWhere('name', $order->method);
                                        @endphp
                                        @if ($methodInfo && isset($methodInfo['image']))
                                            <img src="{{ asset($methodInfo['image']) }}"
                                                alt="{{ $methodInfo['title'] }}"
                                                style="max-width: 100px; margin-right: 15px;">
                                            <span style="font-size: 15px;">{{ $methodInfo['title'] }}</span>
                                        @else
                                            <span style="font-size: 15px;">{{ $order->method }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Danh sách sản phẩm --}}
                                <div class="uk-panel  uk-margin-bottom" style="background: #f8f9fa;">
                                    <h3 class="uk-panel-title uk-text-bold uk-margin-bottom"
                                        style="font-size: 20px; padding: 12px 15px; margin: 0; background: #e8f4f0;">
                                        Sản phẩm đã mua</h3>
                                    <div class="uk-overflow-container" style="background: #fff; padding: 15px;">
                                        <table class="uk-table uk-table-striped uk-table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="uk-text-center">STT</th>
                                                    <th>Sản phẩm</th>
                                                    <th class="uk-text-center">Số lượng</th>
                                                    <th class="uk-text-right">Đơn giá</th>
                                                    <th class="uk-text-right">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->order_products as $index => $item)
                                                    @php
                                                        $option = null;
                                                        if ($item->option) {
                                                            if (is_string($item->option)) {
                                                                $option = json_decode($item->option, true);
                                                            } elseif (is_array($item->option)) {
                                                                $option = $item->option;
                                                            }
                                                        }
                                                        $image = $option['image'] ?? null;
                                                    @endphp
                                                    <tr>
                                                        <td class="uk-text-center" style="font-size: 15px;">
                                                            {{ $index + 1 }}</td>
                                                        <td>
                                                            <div class="uk-flex uk-flex-middle">
                                                                @if ($image)
                                                                    <img src="{{ asset($image) }}"
                                                                        alt="{{ $item->name }}"
                                                                        style="width: 80px; height: 80px; object-fit: contain; border-radius: 4px; margin-right: 12px;">
                                                                @endif
                                                                <div>
                                                                    <div class="uk-text-bold" style="font-size: 15px;">
                                                                        {{ $item->name ?? 'N/A' }}</div>
                                                                    @if ($item->uuid)
                                                                        <div class="uk-text-small uk-text-muted"
                                                                            style="font-size: 13px;">Mã:
                                                                            {{ $item->uuid }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="uk-text-center" style="font-size: 15px;">
                                                            {{ $item->qty ?? 0 }}</td>
                                                        <td class="uk-text-right">
                                                            <span class="uk-text-bold uk-text-primary"
                                                                style="font-size: 15px;">{{ convert_price($item->price ?? 0, true) }}₫</span>
                                                        </td>
                                                        <td class="uk-text-right">
                                                            <strong class="uk-text-bold uk-text-primary"
                                                                style="font-size: 15px;">{{ convert_price(($item->price ?? 0) * ($item->qty ?? 0), true) }}₫</strong>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Tổng tiền --}}
                                <div class="uk-panel uk-margin-bottom" style="background: #fff;">
                                    @php
                                        $cart = is_array($order->cart) ? $order->cart : [];
                                        $promotion = is_array($order->promotion) ? $order->promotion : [];
                                        $cartTotal = $cart['cartTotal'] ?? 0;
                                        $promotionDiscount = $promotion['discount'] ?? 0;
                                        $shipping = $order->shipping ?? 0;
                                        $subTotal = $cartTotal - $promotionDiscount;
                                        $finalTotal = $subTotal + $shipping;
                                    @endphp

                                    <div class="uk-flex uk-flex-middle uk-flex-space-between"
                                        style="padding: 12px 15px; background: #fef0f0; border-bottom: 1px solid #ffcdd2;">

                                        <h3 class="uk-panel-title uk-text-bold"
                                            style="font-size: 20px; margin: 0; color: #d32f2f;">
                                            Tổng tiền
                                        </h3>

                                        {{-- GIÁ TỔNG CỘNG ĐƯA LÊN NGANG --}}
                                        <span class="uk-text-bold" style="font-size: 26px; color: #d32f2f;">
                                            {{ convert_price($finalTotal, true) }}₫
                                        </span>
                                    </div>

                                    <div class="uk-grid uk-grid-small" style="background: #fff; padding: 15px;">
                                        <div class="uk-width-1-1">

                                            @if ($promotionDiscount > 0)
                                                <div class="uk-margin-small" style="padding: 10px 0;">
                                                    <div class="uk-flex uk-flex-space-between">
                                                        <span class="uk-text-bold" style="font-size: 15px;">Giảm
                                                            giá:</span>
                                                        <span class="uk-text-bold uk-text-danger"
                                                            style="font-size: 15px;">
                                                            -{{ convert_price($promotionDiscount, true) }}₫
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($shipping > 0)
                                                <div class="uk-margin-small" style="padding: 10px 0;">
                                                    <div class="uk-flex uk-flex-space-between">
                                                        <span class="uk-text-bold" style="font-size: 15px;">Phí vận
                                                            chuyển:</span>
                                                        <span class="uk-text-bold" style="font-size: 15px;">
                                                            {{ convert_price($shipping, true) }}₫
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                                {{-- Điểm tích lũy --}}
                                @if ($order->point_added || $order->point_used)
                                    <div class="uk-panel  uk-margin-bottom" style="background: #f8f9fa;">
                                        <h3 class="uk-panel-title uk-text-bold uk-margin-bottom"
                                            style="font-size: 20px; padding: 12px 15px; margin: 0; background: #e8f4f0;">
                                            Điểm tích lũy</h3>
                                        <div class="uk-grid uk-grid-small" style="background: #fff; padding: 15px;">
                                            <div class="uk-width-1-1">
                                                @if ($order->point_used > 0)
                                                    <div class="uk-margin-small" style="padding: 8px 0;">
                                                        <span class="uk-text-bold uk-display-inline-block"
                                                            style="min-width: 180px; font-size: 15px;">Điểm đã sử
                                                            dụng:</span>
                                                        <span class="uk-text-danger"
                                                            style="font-size: 15px;">-{{ number_format($order->point_used) }}
                                                            điểm</span>
                                                    </div>
                                                @endif
                                                @if ($order->point_added && $order->point_value > 0)
                                                    <div class="uk-margin-small" style="padding: 8px 0;">
                                                        <span class="uk-text-bold uk-display-inline-block"
                                                            style="min-width: 180px; font-size: 15px;">Điểm được
                                                            cộng:</span>
                                                        <span class="uk-text-success"
                                                            style="font-size: 15px;">+{{ number_format($order->point_value) }}
                                                            điểm</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="uk-margin-top uk-text-right">
                                    <a href="{{ route('customer.order') }}" class="uk-button uk-button-large">
                                        <i class="uk-icon-arrow-left"></i> Quay lại danh sách
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.querySelector('.logout-btn')?.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn đăng xuất không?')) {
                window.location.href = "{{ route('customer.logout') }}";
            }
        });
    </script>
@endsection
<style>
    table td {
        vertical-align: middle !important;
    }
</style>F
