<?php
namespace App\Classes;

class System{

    public function config(){
        $data['homepage'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin chung của website. Tên thương hiệu hiệu website, Logo, Favicon, vv...',
            'value' => [
                'company' => ['type' => 'text', 'label' => 'Tên công ty'],
                'brand' => ['type' => 'text', 'label' => 'Tên thương hiệu'],
                'slogan' => ['type' => 'text', 'label' => 'Slogan'],
                'logo' => ['type' => 'images', 'label' => 'Logo Website', 'title' => 'Click vào ô phía dưới để tải logo'],
                'logo_mobile' => ['type' => 'images', 'label' => 'Logo Mobile', 'title' => 'Click vào ô phía dưới để tải logo'],
                'favicon' => ['type' => 'images', 'label' => 'Favicon', 'title' => 'Click vào ô phía dưới để tải logo'],
                'copyright' => ['type' => 'text', 'label' => 'Copyright'],
                'flashSale' => ['type' => 'text', 'label' => 'Khuyến mãi'],
                'website' => [
                    'type' => 'select', 
                    'label' => 'Tình trạng website',
                    'option' => [
                        'open' => 'Mở cửa website',
                        'close' => 'Website đang bảo trì'
                    ]
                ],
                'video_youtube_pc' => [
                    'type' => 'textarea', 
                    'label' => 'Video youtube(pc)', 
                ],
                'about_video_title' => ['type' => 'text', 'label' => 'Tiêu đề Video Trang Giới Thiệu'],
                'about_video_desc' => ['type' => 'textarea', 'label' => 'Mô tả Video Trang Giới Thiệu'],
                'about_video_url' => ['type' => 'textarea', 'label' => 'Mã nhúng Youtube Video Trang Giới Thiệu'],
                'viettelpost_email' => ['type' => 'text', 'label' => 'Email Viettel Post'],
                'viettelpost_password' => ['type' => 'text', 'label' => 'Password Viettel Post'],
                'download_text' => ['type' => 'text', 'label' => 'Chữ nút Tải tài liệu'],
                'download_link' => ['type' => 'text', 'label' => 'Link nút Tải tài liệu'],
                'warranty_title' => ['type' => 'text', 'label' => 'Tiêu đề bảo hành giá'],
                'warranty_image' => ['type' => 'images', 'label' => 'Ảnh chứng thực/icon bảo hành giá'],
                'warranty_desc' => ['type' => 'textarea', 'label' => 'Nội dung bảo hành giá'],
                'intro_youtube' => ['type' => 'text', 'label' => 'Link Youtube giới thiệu'],
                'intro_tiktok' => ['type' => 'text', 'label' => 'Link Tiktok giới thiệu'],
                'intro_image' => ['type' => 'images', 'label' => 'Ảnh cửa hàng giới thiệu'],
            ]
        ];

        $data['contact'] = [
            'label' => 'Thông tin liên hệ',
            'description' => 'Cài đặt thông tin liên hệ của website ví dụ: Địa chỉ công ty, Văn phòng giao dịch, Hotline, Bản đồ, vv...',
            'value' => [
                'office' => ['type' => 'text', 'label' => 'Địa chỉ công ty'],
                'office_map' => [
                    'type' => 'textarea', 
                    'label' => 'Bản đồ công ty',
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => 'https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/',
                        'target' => '_blank'
                    ]
                ],
                'address' => ['type' => 'text', 'label' => 'Văn phòng giao dịch'],
                'hotline' => ['type' => 'text', 'label' => 'Hotline'],
                'address_mt' => ['type' => 'text', 'label' => 'Địa chỉ Miền trung'],
                'hotline_mt' => ['type' => 'text', 'label' => 'Hotline Miền Trung'],
                'address_mn' => ['type' => 'text', 'label' => 'Địa chỉ Miền Nam'],
                'hotline_mn' => ['type' => 'text', 'label' => 'Hotline Miền Nam'],
                'technical_phone' => ['type' => 'text', 'label' => 'Hotline kỹ thuật'],
                'sell_phone' => ['type' => 'text', 'label' => 'Hotline kinh doanh'],
                'phone' => ['type' => 'text', 'label' => 'Số cố định'],
                'fax' => ['type' => 'text', 'label' => 'Fax'],
                'email' => ['type' => 'text', 'label' => 'Email'],
                'website' => ['type' => 'text', 'label' => 'Website'],
                'map' => [
                    'type' => 'textarea', 
                    'label' => 'Bản đồ', 
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'href' => 'https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/',
                        'target' => '_blank'
                    ]
                ],
                'intro' => ['type' => 'textarea', 'label' => 'Giới thiệu'],
                'working_hours' => [
                    'type' => 'textarea',
                    'label' => 'Giờ làm việc (trang Liên hệ)',
                    'title' => 'Mỗi dòng một mốc, dạng "Thứ 2 – Thứ 6: 8:00 – 18:00". Để trống thì ẩn khối này.',
                ],
                'showroom_hours' => ['type' => 'text', 'label' => 'Giờ mở cửa Showroom'],
                'showroom_1' => ['type' => 'textarea', 'label' => 'Showroom 1 (Địa chỉ + SĐT)'],
                'showroom_2' => ['type' => 'textarea', 'label' => 'Showroom 2 (Địa chỉ + SĐT)'],
                'showroom_3' => ['type' => 'textarea', 'label' => 'Showroom 3 (Địa chỉ + SĐT)'],
                'contact_buy' => ['type' => 'text', 'label' => 'Gọi mua hàng'],
                'contact_support' => ['type' => 'text', 'label' => 'Gọi tư vấn kỹ thuật'],
                'contact_warranty' => ['type' => 'text', 'label' => 'Gọi bảo hành'],
                'contact_biz' => ['type' => 'text', 'label' => 'Hợp tác kinh doanh'],
                'footer_shipping_link' => ['type' => 'text', 'label' => 'Link chính sách vận chuyển'],
                'footer_shop_link' => ['type' => 'text', 'label' => 'Link địa chỉ shop'],
                'footer_privacy_link' => ['type' => 'text', 'label' => 'Link chính sách bảo mật'],
                'footer_warranty_link' => ['type' => 'text', 'label' => 'Link chính sách bảo hành'],
                'footer_policy_link' => ['type' => 'text', 'label' => 'Link trang 8 chính sách khách hàng'],
                'footer_license' => ['type' => 'textarea', 'label' => 'Giấy phép kinh doanh (nội dung)'],
                'footer_bct_logo' => ['type' => 'images', 'label' => 'Logo Bộ Công Thương'],
                'footer_bct_link' => ['type' => 'text', 'label' => 'Link đăng ký Bộ Công Thương'],
                'footer_copyright' => ['type' => 'text', 'label' => 'Copyright (Gomhang)'],
            ]
        ];
       

        $data['seo'] = [
            'label' => 'Cấu hình SEO dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về SEO của trang chủ website. Bao gồm tiêu đề SEO, Từ Khóa SEO, Mô Tả SEO, Meta images',
            'value' => [
                'meta_title' => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword' => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description' => ['type' => 'textarea', 'label' => 'Mô tả SEO'],
                'meta_images' => ['type' => 'images', 'label' => 'Ảnh SEO'],
            ]
        ];

        $data['social'] = [
            'label' => 'Cấu hình Mạng xã hội dành cho trang chủ',
            'description' => 'Cài đặt đầy đủ thông tin về Mạng xã hội của trang chủ website. Bao gồm tiêu đề Mạng xã hội, Từ Khóa SEO, Mô Tả SEO, Meta images',
            'value' => [
                'facebook' => ['type' => 'text', 'label' => 'Facebook'],
                'facebook_image' => ['type' => 'images', 'label' => 'Ảnh Fanpage'],
                'google' => ['type' => 'text', 'label' => 'Google'],
                'tiktok' => ['type' => 'text', 'label' => 'Tiktok'],
                'twitter' => ['type' => 'text', 'label' => 'Twitter'],
                'messenger' => ['type' => 'text', 'label' => 'Messenger'],
                'zalo' => ['type' => 'text', 'label' => 'Zalo'],
                'youtube' => ['type' => 'text', 'label' => 'Youtube'],
                'instagram' => ['type' => 'text', 'label' => 'Instagram'],
                'lazada' => ['type' => 'text', 'label' => 'Lazada'],
                'shopee' => ['type' => 'text', 'label' => 'Shopee'],
            ]
        ];

        
        
        $data['script'] = [
            'label' => 'Cấu hình script',
            'description' => '',
            'value' => [
                '1' => ['type' => 'textarea', 'label' => 'Script Head'],
                '2' => ['type' => 'textarea', 'label' => 'Script Body'],
            ]
        ];

       
        return $data;
    }
	
}
