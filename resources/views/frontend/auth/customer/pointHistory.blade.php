@extends('frontend.homepage.layout')

@section('content')
    <div class="profile-wrapper cat-bg">
        <div class="uk-container uk-container-center">

            @include('frontend.auth.customer.components.header')

            <div class="uk-grid uk-grid-medium mt30">
                <div class="uk-width-large-1-4">
                    @include('frontend.auth.customer.components.sidebar')
                </div>

                <div class="uk-width-large-3-4">
                    <div class="panel-profile">
                        <div class="panel-head">
                            <h2 class="heading-2"><span>Lịch sử giao dịch điểm</span></h2>
                            <div class="description">
                                Theo dõi toàn bộ điểm cộng/trừ phát sinh từ các đơn hàng của bạn
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

                            @if ($histories->count() > 0)
                                <div class="uk-overflow-container" style="overflow-x: auto;">
                                    <table class="uk-table uk-table-striped uk-table-hover point-table">
                                        <thead>
                                            <tr>
                                                <th>Mã giao dịch</th>
                                                <th>Đơn hàng</th>
                                                <th>Loại thay đổi</th>
                                                <th class="uk-text-center">Số điểm</th>
                                                <th>Thời gian</th>
                                                <th>Ghi chú</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($histories as $history)
                                                @php
                                                    $isNegative = (float) $history->points < 0;
                                                    $pointValue =
                                                        ($history->points > 0 ? '+' : '') .
                                                        number_format($history->points);
                                                    $typeLabel = $typeLabels[$history->type] ?? ucfirst($history->type);
                                                    $typeTone = match ($history->type) {
                                                        'earn' => 'success',
                                                        'use' => 'danger',
                                                        'revertEarn' => 'warning',
                                                        'revertUse', 'revertUsed' => 'warning',
                                                        default => 'muted',
                                                    };
                                                    $orderCode = optional($history->order)->code;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>#{{ str_pad($history->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                                    </td>
                                                    <td>
                                                        @if ($orderCode)
                                                            <div>
                                                                <a href="{{ route('customer.order.detail', $orderCode) }}"
                                                                    class="uk-link-reset">
                                                                    <strong>#{{ $orderCode }}</strong>
                                                                </a>
                                                            </div>
                                                            <div class="uk-text-muted uk-text-small">Order ID:
                                                                {{ $history->order_id }}</div>
                                                        @else
                                                            <span class="uk-text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="uk-text-{{ $typeTone }}">
                                                            {{ $typeLabel }}
                                                        </span>
                                                    </td>

                                                    <td class="uk-text-center">
                                                        <span
                                                            class="point-chip {{ $isNegative ? 'point-chip--negative' : 'point-chip--positive' }}">
                                                            {{ $pointValue }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ convertDateTime($history->created_at, 'd/m/Y H:i') }}
                                                    </td>
                                                    <td>
                                                        {{ $history->description ?? '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="uk-margin-top">
                                    {{ $histories->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <div class="uk-text-center uk-padding-large">
                                    <div class="uk-margin-bottom">
                                        <i class="uk-icon-history uk-icon-large uk-text-muted"></i>
                                    </div>
                                    <h3 class="uk-margin-small">Chưa có giao dịch nào</h3>
                                    <p class="uk-text-muted">Điểm thưởng sẽ xuất hiện ngay khi bạn hoàn tất một đơn hàng.
                                    </p>
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
    .point-table th,
    .point-table td {
        vertical-align: middle !important;
        font-size: 15px;
    }

    .type-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
    }

    .type-badge--success {
        background: #e8f7ef;
        color: #1b5e20;
    }

    .type-badge--danger {
        background: #fdecea;
        color: #b71c1c;
    }

    .type-badge--warning {
        background: #fff3cd;
        color: #8a6d3b;
    }

    .type-badge--muted {
        background: #eceff1;
        color: #455a64;
    }

    .point-chip {
        display: inline-block;
        min-width: 120px;
        padding: 8px 12px;
        border-radius: 6px;
        font-weight: 600;
        text-align: center;
    }

    .point-chip--negative {
        background: #fdecec;
        color: #c62828;
    }

    .point-chip--positive {
        background: #e8f5e9;
        color: #2e7d32;
    }
</style>
