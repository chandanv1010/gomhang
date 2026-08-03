@extends('frontend.homepage.layout')

@section('content')
    <div class="compare-page page-body">
        <div class="uk-container uk-container-center">
            <div class="compare-head uk-text-center uk-margin-large">
                <h1>Bảng so sánh sản phẩm</h1>
                <p>
                    Đã chọn
                    <strong class="compare-total-text">{{ $compareTotal }}</strong> /
                    <strong>{{ $maxCompareItems }}</strong>
                    sản phẩm. Chạm vào dấu cộng để thêm sản phẩm cần so sánh.
                </p>
            </div>

            <div class="compare-table-card">
                <div class="compare-table-wrapper" data-compare-search="{{ route('product.compare.search') }}"
                    data-compare-add="{{ route('product.compare.add') }}"
                    data-compare-remove="{{ route('product.compare.remove') }}"
                    data-compare-list="{{ route('product.compare.list') }}" data-limit="{{ $maxCompareItems }}">
                    @include('frontend.product.catalogue.component.compare-table', [
                        'compareSlots' => $compareSlots,
                        'compareFields' => $compareFields,
                    ])
                </div>
            </div>
        </div>
    </div>

    <div id="compare-modal" class="uk-modal">
        <div class="uk-modal-dialog compare-modal">
            <a class="uk-modal-close uk-close"></a>
            <h3 class="uk-text-center">Chọn sản phẩm</h3>
            <div class="compare-search-box">
                <input type="text" id="compare-search-input" class="uk-width-1-1" placeholder="Nhập tên sản phẩm để tìm"
                    autocomplete="off">
            </div>
            <div class="compare-search-results" data-empty="Không tìm thấy sản phẩm phù hợp">
                <div class="compare-search-placeholder">
                    Gõ từ khóa để bắt đầu tìm kiếm.
                </div>
            </div>
        </div>
    </div>
@endsection

