{{--
    Box "Bảo hành giá" ở cột phải, dùng chung cho trang chủ và trang danh mục.

    Trước đây markup này bị chép cứng ở cả hai nơi, tiêu đề và ảnh ghi thẳng vào
    code nên sửa trong admin không có tác dụng. Ba khoá dưới đây vốn đã có sẵn
    trong form admin (App\Classes\System, nhóm "Thông tin chung"), chỉ là view
    không đọc tới.

    Nội dung để trống trong admin thì rơi về đoạn "CAM KẾT 4 TỐT" mặc định,
    tránh trường hợp xoá nhầm là box trắng trơn.
--}}
@php
    $warrantyTitle = trim((string) ($system['homepage_warranty_title'] ?? '')) ?: 'BẢO HÀNH GIÁ';
    $warrantyImage = trim((string) ($system['homepage_warranty_image'] ?? '')) ?: '/userfiles/image/slide/warranty_seal.png';
    $warrantyDesc  = trim((string) ($system['homepage_warranty_desc'] ?? ''));
@endphp

<div class="sidebar-box warranty-box-container mb20">
    <div class="warranty-box-header">
        <img src="{{ $system['homepage_logo'] ?? '' }}" alt="" class="warranty-header-icon"
             onerror="this.style.display='none'">
        <h3>{{ $warrantyTitle }}</h3>
    </div>
    <div class="warranty-box-body">
        <div class="warranty-seal-img-box">
            <img src="{{ $warrantyImage }}" alt="{{ $warrantyTitle }}"
                 onerror="this.style.display='none'">
        </div>
        <div class="warranty-desc-content"
             style="text-align: left; font-size: 13px; line-height: 1.6; color: #444;">
            @if($warrantyDesc !== '')
                {{-- Ô này là textarea trong admin, người dùng có gõ thẻ HTML để
                     bôi đậm/xuống dòng nên phải in thô, không escape. --}}
                {!! $warrantyDesc !!}
            @else
                <p style="margin-top: 0; font-weight: bold; color: #e01b24; text-align: center;">CAM KẾT 4 TỐT:</p>
                <ul style="padding-left: 15px; margin: 0; list-style-type: disc;">
                    <li style="margin-bottom: 5px;"><b>Sản phẩm Tốt:</b> Nguồn gốc rõ ràng, chất lượng kiểm định kĩ càng.</li>
                    <li style="margin-bottom: 5px;"><b>Dịch vụ Tốt:</b> Giao hàng nhanh chóng, tư vấn tận tâm.</li>
                    <li style="margin-bottom: 5px;"><b>Bảo hành Tốt:</b> Lỗi 1 đổi 1 nhanh chóng, uy tín.</li>
                    <li style="margin-bottom: 5px;"><b>Giá thành Tốt:</b> Cam kết mức giá cạnh tranh nhất.</li>
                </ul>
            @endif
        </div>
    </div>
</div>
