<?php
namespace App\Classes;

class Introduce{

    public function config(){
        $data['block_1'] = [
            'label' => 'Khối 1: Banner chính & Thống kê',
            'description' => 'Cài đặt hình nền, tiêu đề chính, các con số thống kê và liên kết nút hành động',
            'value' => [
                'banner_bg' => ['type' => 'images', 'label' => 'Ảnh Banner nền (Vector.png)'],
                'banner_title' => ['type' => 'text', 'label' => 'Tiêu đề lớn trên banner (VIREX VIỆT NAM)'],
                
                'stat_num_1' => ['type' => 'text', 'label' => 'Thống kê 1 - Số (100+)'],
                'stat_lbl_1' => ['type' => 'text', 'label' => 'Thống kê 1 - Nhãn (Chuyên gia)'],
                
                'stat_num_2' => ['type' => 'text', 'label' => 'Thống kê 2 - Số (10+)'],
                'stat_lbl_2' => ['type' => 'text', 'label' => 'Thống kê 2 - Nhãn (năm kinh nghiệm)'],
                
                'stat_num_3' => ['type' => 'text', 'label' => 'Thống kê 3 - Số (1)'],
                'stat_lbl_3' => ['type' => 'text', 'label' => 'Thống kê 3 - Nhãn (Tổ chức hàng đầu)'],
                
                'btn_text_1' => ['type' => 'text', 'label' => 'Nút 1 - Nhãn (Chuyên gia tư vấn)'],
                'btn_link_1' => ['type' => 'text', 'label' => 'Nút 1 - Đường dẫn'],
                
                'btn_text_2' => ['type' => 'text', 'label' => 'Nút 2 - Nhãn (Sản phẩm)'],
                'btn_link_2' => ['type' => 'text', 'label' => 'Nút 2 - Đường dẫn'],
            ]
        ];

        $data['block_2'] = [
            'label' => 'Khối 2: Năng lực kho bãi',
            'description' => 'Cấu hình tiêu đề, mô tả và tối đa 8 ảnh năng lực kho bãi',
            'value' => [
                'warehouse_title' => ['type' => 'text', 'label' => 'Tiêu đề khối (Năng lực kho bãi)'],
                'warehouse_desc' => ['type' => 'text', 'label' => 'Mô tả chi tiết kho bãi'],
                
                'warehouse_img_1' => ['type' => 'images', 'label' => 'Ảnh kho bãi 1'],
                'warehouse_img_2' => ['type' => 'images', 'label' => 'Ảnh kho bãi 2'],
                'warehouse_img_3' => ['type' => 'images', 'label' => 'Ảnh kho bãi 3'],
                'warehouse_img_4' => ['type' => 'images', 'label' => 'Ảnh kho bãi 4'],
                'warehouse_img_5' => ['type' => 'images', 'label' => 'Ảnh kho bãi 5'],
                'warehouse_img_6' => ['type' => 'images', 'label' => 'Ảnh kho bãi 6'],
                'warehouse_img_7' => ['type' => 'images', 'label' => 'Ảnh kho bãi 7'],
                'warehouse_img_8' => ['type' => 'images', 'label' => 'Ảnh kho bãi 8'],
            ]
        ];

        $data['block_3'] = [
            'label' => 'Khối 3: Tại sao nên lựa chọn VIREX',
            'description' => 'Cấu hình tiêu đề khối và 4 thẻ thông tin lý do lựa chọn',
            'value' => [
                'why_title' => ['type' => 'text', 'label' => 'Tiêu đề khối (TẠI SAO NÊN LỰA CHỌN VIREX)'],
                
                // Card 1
                'why_icon_1' => ['type' => 'images', 'label' => 'Mục 1 - Icon'],
                'why_title_1' => ['type' => 'text', 'label' => 'Mục 1 - Tiêu đề'],
                'why_desc_1' => ['type' => 'textarea', 'label' => 'Mục 1 - Nội dung'],
                
                // Card 2
                'why_icon_2' => ['type' => 'images', 'label' => 'Mục 2 - Icon'],
                'why_title_2' => ['type' => 'text', 'label' => 'Mục 2 - Tiêu đề'],
                'why_desc_2' => ['type' => 'textarea', 'label' => 'Mục 2 - Nội dung'],
                
                // Card 3
                'why_icon_3' => ['type' => 'images', 'label' => 'Mục 3 - Icon'],
                'why_title_3' => ['type' => 'text', 'label' => 'Mục 3 - Tiêu đề'],
                'why_desc_3' => ['type' => 'textarea', 'label' => 'Mục 3 - Nội dung'],
                
                // Card 4
                'why_icon_4' => ['type' => 'images', 'label' => 'Mục 4 - Icon'],
                'why_title_4' => ['type' => 'text', 'label' => 'Mục 4 - Tiêu đề'],
                'why_desc_4' => ['type' => 'textarea', 'label' => 'Mục 4 - Nội dung'],
            ]
        ];

        $data['block_4'] = [
            'label' => 'Khối 4: Giải pháp & Liên hệ',
            'description' => 'Cấu hình tiêu đề khối và 3 hộp thông tin liên hệ',
            'value' => [
                'contact_title' => ['type' => 'text', 'label' => 'Tiêu đề khối (Bạn đang cần giải pháp phù hợp)'],
                'contact_desc' => ['type' => 'text', 'label' => 'Mô tả ngắn'],
                
                // Option 1
                'contact_title_1' => ['type' => 'text', 'label' => 'Hộp 1 - Tiêu đề (Hotline tư vấn trực tiếp)'],
                'contact_btn_text_1' => ['type' => 'text', 'label' => 'Hộp 1 - Chữ trên nút/Giá trị (0966.000.643)'],
                'contact_link_1' => ['type' => 'text', 'label' => 'Hộp 1 - Link liên kết (tel:0966000643)'],
                
                // Option 2
                'contact_title_2' => ['type' => 'text', 'label' => 'Hộp 2 - Tiêu đề (Tư vấn khách hàng chat zalo)'],
                'contact_btn_text_2' => ['type' => 'text', 'label' => 'Hộp 2 - Chữ trên nút/Giá trị (Chat Zalo)'],
                'contact_link_2' => ['type' => 'text', 'label' => 'Hộp 2 - Link liên kết (zalo.me/...)'],
                
                // Option 3
                'contact_title_3' => ['type' => 'text', 'label' => 'Hộp 3 - Tiêu đề (Đội ngũ tư vấn)'],
                'contact_btn_text_3' => ['type' => 'text', 'label' => 'Hộp 3 - Chữ trên nút/Giá trị (Yêu cầu liên hệ)'],
                'contact_link_3' => ['type' => 'text', 'label' => 'Hộp 3 - Link liên kết'],
            ]
        ];

        return $data;
    }
	
}
