<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Classes\Nestedsetbie;
use App\Traits\HasNested;
use App\Traits\HasRouter;

class ScholarCatalogueSeeder extends Seeder
{
    use HasNested, HasRouter;

    protected $nestedset;

    public function run()
    {
        DB::table('scholar_catalogue_language')->delete();
        DB::table('scholar_catalogues')->delete();
        
        $data = [
            [
                'catalogue' => [
                    'parent_id' => 0,
                    'lft' => 10,
                    'rgt' => 11,
                    'level' => 1,
                    'image' => null,
                    'icon' => null,
                    'album' => null,
                    'publish' => 2,
                    'order' => 10,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 18, 15, 1, 33),
                    'updated_at' => Carbon::create(2025, 9, 18, 13, 26, 48),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng trường',
                    'canonical' => 'hoc_bổng_trường',
                    'description' => '<p>Học bổng trường</p>',
                    'content' => '<p>Học bổng trường</p>',
                    'meta_title' => 'test',
                    'meta_keyword' => 'test',
                    'meta_description' => 'test',
                ]
            ],
            [
                'catalogue' => [
                    'parent_id' => 0,
                    'lft' => 12,
                    'rgt' => 13,
                    'level' => 1,
                    'image' => '/userfiles/image/0fc3c932-2a2b-495e-b426-bcb53d702...',
                    'icon' => '/userfiles/image/012bce38-16ed-4c81-80ee-2aed...',
                    'album' => null,
                    'publish' => 2,
                    'order' => 0,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 19, 12, 36, 31),
                    'updated_at' => Carbon::create(2025, 9, 19, 13, 26, 48),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng khác',
                    'canonical' => 'hoc-bong-khac',
                    'description' => null,
                    'content' => null,
                    'meta_title' => 'Tes hcoj bong 2',
                    'meta_keyword' => 'Tes hcoj bong 2',
                    'meta_description' => 'Tes hcoj bong 2',
                ]
            ],
            [
                'catalogue' => [
                    'parent_id' => 0,
                    'lft' => 8,
                    'rgt' => 9,
                    'level' => 1,
                    'image' => null,
                    'icon' => null,
                    'album' => null,
                    'publish' => 2,
                    'order' => 0,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 19, 13, 32, 42),
                    'updated_at' => Carbon::create(2025, 9, 19, 13, 32, 42),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng thành phố',
                    'canonical' => 'hoc-bong-thanh-pho',
                    'description' => null,
                    'content' => null,
                    'meta_title' => null,
                    'meta_keyword' => null,
                    'meta_description' => null,
                ]
            ],
            [
                'catalogue' => [
                    'parent_id' => 0,
                    'lft' => 6,
                    'rgt' => 7,
                    'level' => 1,
                    'image' => null,
                    'icon' => null,
                    'album' => null,
                    'publish' => 2,
                    'order' => 0,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 19, 13, 32, 50),
                    'updated_at' => Carbon::create(2025, 9, 19, 13, 32, 50),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học Bổng Tỉnh',
                    'canonical' => 'hoc-bong-tinh',
                    'description' => null,
                    'content' => null,
                    'meta_title' => null,
                    'meta_keyword' => null,
                    'meta_description' => null,
                ]
            ],
            [
                'catalogue' => [
                    'parent_id' => 0,
                    'lft' => 4,
                    'rgt' => 5,
                    'level' => 1,
                    'image' => null,
                    'icon' => null,
                    'album' => null,
                    'publish' => 2,
                    'order' => 0,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 19, 13, 33, 1),
                    'updated_at' => Carbon::create(2025, 9, 19, 13, 33, 1),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng không từ',
                    'canonical' => 'hoc-bong-khong-tu',
                    'description' => null,
                    'content' => null,
                    'meta_title' => null,
                    'meta_keyword' => null,
                    'meta_description' => null,
                ]
            ],
            [
                'catalogue' => [
                    'parent_id' => 0,
                    'lft' => 2,
                    'rgt' => 3,
                    'level' => 1,
                    'image' => null,
                    'icon' => null,
                    'album' => null,
                    'publish' => 2,
                    'order' => 0,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 19, 13, 33, 10),
                    'updated_at' => Carbon::create(2025, 9, 19, 13, 33, 10),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng chính phủ Trung Quốc',
                    'canonical' => 'hoc-bong-chinh-phu-trung-quoc',
                    'description' => null,
                    'content' => null,
                    'meta_title' => null,
                    'meta_keyword' => null,
                    'meta_description' => null,
                ]
            ],
        ];

        foreach ($data as $item) {
            $catalogueId = DB::table('scholar_catalogues')->insertGetId($item['catalogue']);
            $item['language']['scholar_catalogue_id'] = $catalogueId;
            DB::table('scholar_catalogue_language')->insert($item['language']);
            $routerData = $this->createRouterPayload(
                $item['language']['canonical'],
                $catalogueId,
                $item['language']['language_id'],
                'ScholarCatalogueController'
            );
            DB::table('routers')->updateOrInsert(
                [
                    'canonical' => $routerData['canonical'],
                    'language_id' => $routerData['language_id'],
                ],
                $routerData
            );

        }

        $this->nestedset = new Nestedsetbie([
            'table' => 'scholar_catalogues',
            'foreignkey' => 'scholar_catalogue_id',
            'language_id' =>  1 ,
        ]);
        $this->nestedSet();

    }
}