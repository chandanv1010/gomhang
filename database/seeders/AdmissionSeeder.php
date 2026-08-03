<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\HasRouter;

class AdmissionSeeder extends Seeder
{
    use HasRouter;

    public function run()
    {
        DB::table('admission_language')->delete();
        DB::table('admissions')->delete();
        
        $data = [
            [
                'admission' => [
                    'admissions_info' => '{"season":"2024","admission_time":"th\u00e1ng 9","apply_deadline":"2025-09-20","position":"sinh vi\u00ean , nghi\u00ean c\u1ee9u sinh","application_fee":"900.000","education_mode":"ngo\u1ea1i tuy\u1ebfn"}',
                    'submission_time' => '2025-09-20 00:00:00',
                    'image' => '/userfiles/image/b08398cd-1d71-4349-949c-41db88e34a56.jpeg',
                    'album' => '["\/userfiles\/image\/012bce38-16ed-4c81-80ee-2aede353ee72.jpeg","\/userfiles\/image\/0fc3c932-2a2b-499e-b426-bcb53d70257d.jpeg","\/userfiles\/image\/1ea56f1a-a21a-40c4-97a6-f70c5178833d.jpeg","\/userfiles\/image\/25a8f42d-77af-4266-8f32-592a1c2e96a0.jpeg","\/userfiles\/image\/449d086e-f90e-436e-856b-ff28ad6abf30.jpeg"]',
                    'admission_catalogue_id' => 26,
                    'scholar_id' => 1,
                    'publish' => 2,
                    'order' => 2,
                    'user_id' => 4504,
                    'deleted_at' => null,
                    'created_at' => Carbon::create(2025, 9, 20, 18, 36, 19),
                    'updated_at' => Carbon::create(2025, 9, 20, 18, 36, 19),
                ],
                'language' => [
                    'language_id' => 1,
                    'name' => 'Hướng dẫn tuyển sinh sinh viên quốc tế của Đại học Y khoa Trung Quốc Quảng Tây năm 2024',
                    'canonical' => 'huong-dan-tuyen-sinh-sinh-vien-quoc-te-cua-dai-hoc-y-khoa-trung-quoc-quang-tay-nam-2024',
                    'description' => '<p><font dir="auto" style="vertical-align: inherit;"><font dir="auto" style="vertical-align: inherit;">Đại học Y học cổ truyền Quảng T&acirc;y đang tuyển sinh chương tr&igrave;nh Cử nh&acirc;n, Thạc sĩ v&agrave; Tiến sĩ v&agrave;o năm 2024. Xem chi tiết b&ecirc;n dưới!</font></font></p>',
                    'content' => null,
                    'meta_title' => 'test',
                    'meta_keyword' => 'test',
                    'meta_description' => 'test',
                ],
                'train' => [
                    'train_id' => 12
                ]
            ],
        ];

        foreach ($data as $item) {
            $id = DB::table('admissions')->insertGetId($item['admission']);
            $item['language']['admission_id'] = $id;
            $item['train']['admission_id'] = $id;
            DB::table('admission_language')->insert($item['language']);
            DB::table('admission_train')->insert($item['train']);
            $routerData = $this->createRouterPayload(
                $item['language']['canonical'],
                $id,
                $item['language']['language_id'],
                'AdmissionController'
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