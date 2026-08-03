<?php   
return [
    'module' => [
        [
            'title' => 'Quản lý tài khoản',
            'subModule' => [
                [
                    'title' => 'Cập nhật thông tin',
                    'icon' => 'fi-rs-user',
                    'route' => route('buyer.profile')
                ],
                [
                    'title' => 'Thay đổi mật khẩu',
                    'icon' => 'fi-rs-key',
                    'route' => route('buyer.profile.password')
                ],
                [
                    'title' => 'Đăng xuất',
                    'icon' => 'fa fa-sign-out',
                    'route' => route('buyer.logout')
                ]
            ]
        ],
        [
            'title' => 'Quản lý đơn hàng',
            'subModule' => [
                [
                    'title' => 'Danh sách đơn hàng',
                    'icon' => 'fi-rs-shopping-cart',
                    'route' => route('buyer.order')
                ],
            ]
        ],
    ]
];
