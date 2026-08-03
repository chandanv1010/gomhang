<?php   

    return [
        'model' => [
            'PostCatalogue' => 'Nhóm Bài Viết',
            'Post' => 'Bài Viết',
            'ProductCatalogue' => 'Nhóm Sản Phẩm',
            'Product' => 'Sản Phẩm',
            // 'ScholarCatalogue' => 'Nhóm Học Bổng',
            // 'Scholar' => 'Học Bổng',
            // 'MajorCatalogue' => 'Nhóm Chuyên Ngành',
            // 'Major' => 'Chuyên Ngành',
        ],
        'type' => [
            'dropdown-menu' => 'Dropdown Menu',
            'mega-menu' => 'Mega Menu'
        ],
        'effect' => [
            'fade' => 'Fade',
            'cube' => 'Cube',
            'coverflow' => 'Coverflow',
            'flip' => 'Flip',
            'cards' => 'Cards',
            'creative' => 'Creative',
        ],
        'navigate' => [
            'hide' => 'Ẩn',
            'dots' => 'Dấu chấm',
            'thumbnails' => 'Ảnh Thumbnails'
        ],
        'promotion' => [
            'order_amount_range' => 'Chiết khấu theo tổng giá trị đơn hàng',
            'product_and_quantity' => 'Chiết khấu theo từng sản phẩm',
            'buy_product_take_gift' => 'Mua sản phẩm - tặng sản phẩm',
            // 'buy_combo' => 'Mua theo combo'
        ],
        'item' => [
            'Product' => 'Phiên bản sản phẩm',
            'ProductCatalogue' => 'Loại sản phẩm',
        ],
        'gender' => [
            [
                'id' => 1,
                'name' => 'Nam'
            ],
            [
                'id' => 2,
                'name' => 'Nữ'
            ],
        ],
        'day' => array_map(function($value){
            return ['id' => $value-1, 'name' => $value];
        }, range(1, 31)),
        'applyStatus' => [
            [
                'id' => 'staff_take_care_customer',
                'name' => 'Nhân viên phụ trách'
            ],
            [
                'id' => 'customer_group',
                'name' => 'Nhóm khách hàng'
            ],
            [
                'id' => 'customer_gender',
                'name' => 'Giới tính'
            ],
            [
                'id' => 'customer_birthday',
                'name' => 'Ngày sinh'
            ],
        ],
        'voucher' => [
            'TOTAL_ORDERS' => 'Voucher tổng giá trị đơn hàng',
            'PRODUCT_VOUCHER' => 'Voucher sản phẩm',
            'SHIPPING_ORDERS' => 'Voucher phí vận chuyển'
        ],
        'item_voucher' => [
            'SPECIFIC_PRODUCTS' => 'Sản phẩm cụ thể',
            'ALL_PRODUCTS' => 'Toàn bộ sản phẩm',
        ],
        'buy_product_take_gift' => [
            'single_product' => 'Mua số lượng lớn 1 sản phẩm',
            'multiple_product' => 'Mua nhiều sản phẩm khác nhau'
        ],
        'product_model' => 'Product'
    ];

