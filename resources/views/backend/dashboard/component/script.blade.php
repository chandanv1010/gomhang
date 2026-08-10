<!-- Core JS -->
<script src="{{ asset_v('vendor/backend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset_v('vendor/backend/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset_v('vendor/backend/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
{{-- <script src="{{ asset_v('vendor/backend/plugins/jquery-ui.js') }}"></script> --}}
<script src="{{ asset_v('vendor/backend/js/inspinia.js') }}"></script>
<script src="{{ asset_v('vendor/backend/js/plugins/pace/pace.min.js') }}"></script>
<script src="{{ asset_v('vendor/backend/js/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset_v('vendor/backend/js/plugins/switchery/switchery.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="{{ asset_v('vendor/backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.js') }}"></script>
<script src="{{ asset_v('vendor/backend/js/plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Library -->
<script src="{{ asset_v('vendor/backend/library/dashboard.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/finder.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/location.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/menu.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/order.js') }}"></script>
{{-- promotion.js và voucher.js chỉ dùng cho màn hình khuyến mãi và voucher, nhưng
     trước đây nạp ở MỌI trang admin. Chúng gắn hàng chục handler ở cấp document
     với những class trùng tên trang khác (.ajax-search-item, .search-model,
     .page-link, .deleted...) nên đã làm hỏng chức năng chọn sản phẩm trong widget:
     handler của promotion chạy trước, đảo data-flag, widget.js đọc phải flag sai.

     Chỉ nạp trên đúng route của chúng. Toàn bộ selector của hai file này chỉ xuất
     hiện trong view promotion/* và voucher/*, không nơi nào khác. --}}
@if(request()->routeIs('promotion.*') || request()->routeIs('voucher.*'))
    <script src="{{ asset_v('vendor/backend/library/promotion.js') }}"></script>
    <script src="{{ asset_v('vendor/backend/library/voucher.js') }}"></script>
@endif
<script src="{{ asset_v('vendor/backend/library/report.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/seo.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/slide.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/variant.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/widget.js') }}"></script>
<script src="{{ asset_v('vendor/backend/library/library.js') }}"></script>

<!-- Plugin -->
<script src="{{ asset_v('vendor/backend/plugins/nice-select/js/jquery.nice-select.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="{{ asset_v("vendor/backend/plugins/ckfinder_2/ckfinder.js") }}"></script>
<script type="text/javascript" src="{{ asset_v("vendor/backend/plugins/ckeditor/ckeditor.js") }}"></script>

{{-- @vite('resources/js/app.backend.js') --}}

{{-- @if(isset($config['extendJs']) && $config['extendJs'] === true) --}}

{{-- @endif --}}
<script src="{{ asset_v('backend/plugins/jquery-ui.js') }}"></script>
<script src="{{ asset_v('backend/js/plugins/nestable/jquery.nestable.js') }}"></script>
<script>
    window.moment = moment; // ép vào global để daterangepicker nhận
</script>