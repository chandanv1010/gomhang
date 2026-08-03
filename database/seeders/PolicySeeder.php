<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PolicySeeder extends Seeder
{
    public function run()
    {
        DB::table('scholar_policies')->delete();
        
        $data = [
            [
                'name' => 'Bao ăn 3 bữa tại trường',
                'publish' => 2,
                'order' => 2,
                'user_id' => 4504,
                'deleted_at' => null,
                'created_at' => Carbon::create(2025, 9, 19, 23, 10, 02),
                'updated_at' => Carbon::create(2025, 9, 20, 00, 04, 45),
            ],
            [
                'name' => 'Hỗ trợ 12.000 NDT/năm',
                'publish' => 2,
                'order' => 2,
                'user_id' => 4504,
                'deleted_at' => null,
                'created_at' => Carbon::create(2025, 9, 19, 23, 10, 02),
                'updated_at' => Carbon::create(2025, 9, 20, 00, 04, 45),
            ],
            [
                'name' => 'Trợ cấp 3800 tệ/tháng',
                'publish' => 2,
                'order' => 2,
                'user_id' => 4504,
                'deleted_at' => null,
                'created_at' => Carbon::create(2025, 9, 19, 23, 10, 02),
                'updated_at' => Carbon::create(2025, 9, 20, 00, 04, 45),
            ],
            [
                'name' => 'Trợ cấp 1220 tệ/tháng',
                'publish' => 2,
                'order' => 2,
                'user_id' => 4504,
                'deleted_at' => null,
                'created_at' => Carbon::create(2025, 9, 19, 23, 10, 02),
                'updated_at' => Carbon::create(2025, 9, 20, 00, 04, 45),
            ],
            [
                'name' => 'Hỗ trợ 35.000 tệ/năm',
                'publish' => 2,
                'order' => 2,
                'user_id' => 4504,
                'deleted_at' => null,
                'created_at' => Carbon::create(2025, 9, 19, 23, 10, 02),
                'updated_at' => Carbon::create(2025, 9, 20, 00, 04, 45),
            ],
        ];

        foreach ($data as $item) {
            DB::table('scholar_policies')->insertGetId($item);
        }

    }
}