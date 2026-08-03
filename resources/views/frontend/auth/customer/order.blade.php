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
                            <h2 class="heading-2"><span>Đơn hàng đã mua</span></h2>
                            <div class="description">
                                Xem lịch sử và chi tiết các đơn hàng bạn đã đặt
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

                            @if ($orders->count() > 0)
                                <div class="uk-overflow-container" style="overflow-x: auto;">
                                    <table class="uk-table uk-table-striped uk-table-hover" style="min-width: 1400px;">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 120px;">Mã đơn hàng</th>
                                                <th style="min-width: 140px;">Ngày đặt</th>
                                                <th style="min-width: 200px;">Khách Hàng</th>
                                                <th style="min-width: 120px;">Email</th>
                                                <th style="min-width: 100px;">Tỉnh/TP</th>
                                                <th style="min-width: 100px;">Quận/Huyện</th>
                                                <th style="min-width: 100px;">Phường/Xã</th>
                                                <th style="min-width: 100px;" class="uk-text-right">Giảm giá</th>
                                                <th style="min-width: 100px;" class="uk-text-right">Phí ship</th>
                                                <th style="min-width: 120px;" class="uk-text-right">Tổng cộng</th>
                                                <th style="min-width: 120px;">Phương thức</th>
                                                <th style="min-width: 100px;" class="uk-text-center">Trạng thái</th>
                                                <th style="min-width: 120px;" class="uk-text-center">Thanh toán</th>
                                                <th style="min-width: 100px;" class="uk-text-center">Giao hàng</th>
                                                <th style="min-width: 120px;" class="uk-text-center">Điểm tích lũy</th>
                                                <th style="min-width: 130px;" class="uk-text-center">Điểm đã dùng</th>
                                                <th style="min-width: 150px;">Ghi chú</th>
                                                <th style="min-width: 100px;" class="uk-text-center">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($orders as $order)
                                                @php
                                                    $cart = is_array($order->cart) ? $order->cart : [];
                                                    $promotion = is_array($order->promotion) ? $order->promotion : [];
                                                    $cartTotal = $cart['cartTotal'] ?? 0;
                                                    $promotionDiscount = $promotion['discount'] ?? 0;
                                                    $shipping = $order->shipping ?? 0;
                                                    $finalTotal = $cartTotal - $promotionDiscount + $shipping;
                                                    $detailUrl = route('customer.order.detail', $order->code);
                                                @endphp
                                                <tr onclick="window.location='{{ $detailUrl }}';"
                                                    style="cursor: pointer;">
                                                    <td>
                                                        <strong class="uk-text-primary">#{{ $order->code }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ convertDateTime($order->created_at, 'd-m-Y') }}
                                                    </td>
                                                    <td>
                                                        <div><b>N:</b> {{ $order->fullname ?? '-' }}</div>
                                                        <div><b>P:</b> {{ $order->phone ?? '-' }}</div>
                                                        <div><b>A:</b> {{ $order->address ?? '-' }}</div>
                                                    </td>
                                                    <td>{{ $order->email ?? '-' }}</td>
                                                    <td>{{ $order->province_name ?? '-' }}</td>
                                                    <td>{{ $order->district_name ?? '-' }}</td>
                                                    <td>{{ $order->ward_name ?? '-' }}</td>
                                                    <td class="uk-text-right">
                                                        @if ($promotionDiscount > 0)
                                                            <span
                                                                class="uk-text-danger">-{{ convert_price($promotionDiscount, true) }}₫</span>
                                                        @else
                                                            <span class="uk-text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="uk-text-right">
                                                        @if ($shipping > 0)
                                                            <span>{{ convert_price($shipping, true) }}₫</span>
                                                        @else
                                                            <span class="uk-text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="uk-text-right">
                                                        <strong class="uk-text-primary"
                                                            style="font-size: 16px; font-weight: bold;">{{ convert_price($finalTotal, true) }}₫</strong>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $paymentMethods = __('payment.method');
                                                            $methodInfo = collect($paymentMethods)->firstWhere(
                                                                'name',
                                                                $order->method,
                                                            );
                                                        @endphp
                                                        @if ($methodInfo && isset($methodInfo['image']))
                                                            <img src="{{ asset($methodInfo['image']) }}"
                                                                alt="{{ $methodInfo['title'] }}"
                                                                title="{{ $methodInfo['title'] }}"
                                                                style="max-width: 40px; height: auto;">
                                                        @else
                                                            <span
                                                                class="uk-text-muted uk-text-small">{{ $order->method ?? '-' }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="uk-text-center">
                                                        <span
                                                            class="uk-badge uk-badge-{{ $order->confirm == 'confirm' ? 'success' : ($order->confirm == 'cancle' ? 'danger' : 'warning') }}">
                                                            {{ __('order.confirm')[$order->confirm] ?? $order->confirm }}
                                                        </span>
                                                    </td>
                                                    <td class="uk-text-center">
                                                        <span
                                                            class="uk-badge uk-badge-{{ $order->payment == 'paid' ? 'success' : 'warning' }}">
                                                            {{ $order->payment == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                                        </span>
                                                    </td>
                                                    <td class="uk-text-center">
                                                        <span
                                                            class="uk-badge uk-badge-{{ $order->delivery == 'success' ? 'success' : ($order->delivery == 'processing' ? 'primary' : 'warning') }}">
                                                            {{ $order->delivery == 'pending' ? 'Chờ giao hàng' : ($order->delivery == 'processing' ? 'Đang giao hàng' : ($order->delivery == 'success' ? 'Đã giao hàng' : '-')) }}
                                                        </span>
                                                    </td>
                                                    <td class="uk-text-center">
                                                        @if ($order->point_used > 0)
                                                            <div class="uk-text-danger">
                                                                -{{ number_format($order->point_used) }}</div>
                                                        @else
                                                            <span class="uk-text-muted">0</span>
                                                        @endif
                                                    </td>
                                                    <td class="uk-text-center">
                                                        @if ($order->point_added && $order->point_value > 0)
                                                            <div class="uk-text-success">
                                                                +{{ number_format($order->point_value) }}</div>
                                                        @else
                                                            <span class="uk-text-muted">0</span>
                                                        @endif
                                                    </td>
                                                    <td><span
                                                            class="uk-text-muted">{{ Str::limit($order->description ?? '-', 50) }}</span>
                                                    </td>
                                                    <td class="uk-text-center">
                                                        <a href="{{ $detailUrl }}"
                                                            class="uk-button uk-button-small uk-button-primary"
                                                            style="border-radius: 6px;" title="Xem chi tiết">
                                                            <i class="fa fa-eye" style="margin-top: 5px"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div class="uk-margin-top">
                                    {{ $orders->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <div class="uk-text-center uk-padding-large">
                                    <div class="uk-margin-bottom">
                                        <i class="uk-icon-shopping-cart uk-icon-large uk-text-muted"></i>
                                    </div>
                                    <h3 class="uk-margin-small">Chưa có đơn hàng nào</h3>
                                    <p class="uk-text-muted">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
                                    <a href="{{ route('home.index') }}" class="uk-button uk-button-primary">
                                        <i class="uk-icon-shopping-cart"></i> Mua sắm ngay
                                    </a>
                                </div>
                            @endif
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
</style>
