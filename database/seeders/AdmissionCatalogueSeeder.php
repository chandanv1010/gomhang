<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Classes\Nestedsetbie;
use App\Traits\HasNested;
use App\Traits\HasRouter;

class AdmissionCatalogueSeeder extends Seeder
{
    use HasNested, HasRouter;

    protected $nestedset;

    public function run()
    {
        DB::table('admission_catalogue_language')->delete();
        DB::table('admission_catalogues')->delete();
        
        $data = [
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
                    'created_at' => Carbon::create(2025, 9, 20, 11, 13, 23),
                    'updated_at' => Carbon::create(2025, 9, 20, 11, 13, 23),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Học bổng',
                    'canonical' => 'hoc-bong',
                    'description' => null,
                    'content' => null,
                    'meta_title' => 'test',
                    'meta_keyword' => 'test',
                    'meta_description' => 'test',
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
                    'created_at' => Carbon::create(2025, 9, 20, 11, 13, 23),
                    'updated_at' => Carbon::create(2025, 9, 20, 11, 13, 23),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Tự  tài trợ',
                    'canonical' => 'tu-tai-tro',
                    'description' => null,
                    'content' => null,
                    'meta_title' => 'test',
                    'meta_keyword' => 'test',
                    'meta_description' => 'test',
                ]
            ],
        ];

        foreach ($data as $item) {
            $catalogueId = DB::table('admission_catalogues')->insertGetId($item['catalogue']);
            $item['language']['admission_catalogue_id'] = $catalogueId;
            DB::table('admission_catalogue_language')->insert($item['language']);
            $routerData = $this->createRouterPayload(
                $item['language']['canonical'],
                $catalogueId,
                $item['language']['language_id'],
                'AdmissionCatalogueController'
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
            'table' => 'admission_catalogues',
            'foreignkey' => 'admission_catalogue_id',
            'language_id' =>  1 ,
        ]);
        $this->nestedSet();
    }
}