<?php 
	return [
		'status' => [
			'0' => 'Chọn tình trạng',
			'1' => 'Đã kích hoạt',
			'2' => 'Kích hoạt bảo hành',
		],
		'publish' => [
			'0' => 'Chọn tình trạng',
			'1' => 'Không xuất bản',
			'2' => 'Xuất bản',
		],
		'follow' => [
			'1' => 'Follow',
			'2' => 'Nofollow',
			
		],
		'suffix' => '.html',
		'defaultPublish' => ['publish','=', 2],
        /*
         * Attribute catalogue that holds brands ("Thương hiệu"). Products store
         * their brand under this key in the products.attribute JSON column, e.g.
         * {"8": ["25"]}, and the brand filter and brand logos both key off it.
         */
        'brandAttributeCatalogueId' => 8,
        'retail_customer' => 1,
        'time_expried' => 180,
        'google_client_id' => '',
        'google_secret_id' => '',
        'facebook_client_id' => '',
        'facebook_secret_id' => '',
	];
