<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\HasRouter;

class ScholarSeeder extends Seeder
{
    use HasRouter;

    public function run()
    {
        DB::table('scholar_language')->delete();
        DB::table('scholars')->delete();
        
        $data = [
            [
                'scholar' => [
                    'scholar_catalogue_id' => 26,
                    'policy_id' => 1,
                    'train_id' => 1,
                    'scholar_policy' => null,
                    'image' => '/userfiles/image/hoc-bong-truong-dai-hoc-quang-chauy.jpeg',
                    'album' => null,
                    'publish' => 2,
                    'order' => 2,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 19, 23, 10, 02),
                    'updated_at' => Carbon::create(2025, 9, 20, 00, 04, 45),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng sinh viên quốc tế của Đại học Quảng Châu',
                    'canonical' => 'hoc-bong-sinh-vien-quoc-te-cua-dai-hoc-quang-chau',
                    'description' => '<p class="fs-6"><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">Đại học Quảng Ch&acirc;u l&agrave; một trường đại học trọng điểm tọa lạc tại một trong những th&agrave;nh phố ph&aacute;t triển nhất Trung Quốc. H&atilde;y tham gia studyinchina.io để kh&aacute;m ph&aacute; những cơ hội học tập hấp dẫn với Học bổng Đại học Quảng Ch&acirc;u!</font></font></p>',
                    'content' => '<p>Học bổng trường</p>',
                    'meta_title' => 'test',
                    'meta_keyword' => 'test',
                    'meta_description' => 'test',
                ]
            ],
        ];

        foreach ($data as $item) {
            $id = DB::table('scholars')->insertGetId($item['scholar']);
            $item['language']['scholar_id'] = $id;
            DB::table('scholar_language')->insert($item['language']);
            $routerData = $this->createRouterPayload(
                $item['language']['canonical'],
                $id,
                $item['language']['language_id'],
                'ScholarController'
            );
            DB::table('routers')->updateOrInsert(
                [
                    'canonical' => $routerData['canonical'],
                    'language_id' => $routerData['language_id'],
                ],
                $routerData
            );

        }


    }
}