<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Đặt logo thương hiệu vào cấu hình hệ thống.
 *
 * File ảnh nằm sẵn trong repo tại public/userfiles/image/logo/ (đúng thư mục
 * CKFinder đọc), nên chỉ cần `git pull` là ảnh đã có trên host - không phải
 * upload lại qua CKFinder. Seeder này chỉ trỏ systems.homepage_logo tới nó.
 *
 *   php artisan db:seed --class=LogoSeeder
 *
 * Chạy lại nhiều lần vẫn an toàn (idempotent).
 */
class LogoSeeder extends Seeder
{
    /** Đường dẫn public của logo - cũng chính là URL CKFinder sinh ra. */
    private const LOGO_PATH = '/userfiles/image/logo/nkc-logo.png';

    public function run(): void
    {
        $absolute = public_path(ltrim(self::LOGO_PATH, '/'));

        if (!is_file($absolute)) {
            // Không có file thì đừng ghi DB, nếu không header sẽ thành ảnh vỡ.
            $this->command->error('Không tìm thấy ' . self::LOGO_PATH . ' - kiểm tra lại đã pull đủ code chưa.');
            return;
        }

        // Chỉ ghi cho ngôn ngữ còn sống trong bảng languages. Bảng systems còn sót
        // các dòng của ngôn ngữ đã xoá mềm và cả language_id = 0 - frontend không
        // bao giờ đọc tới, ghi vào đó chỉ làm nhiễu.
        $languageIds = DB::table('languages')->whereNull('deleted_at')->pluck('id');

        if ($languageIds->isEmpty()) {
            $this->command->error('Không có ngôn ngữ nào trong bảng languages - hãy tạo ngôn ngữ trong admin trước.');
            return;
        }

        $userId = DB::table('users')->min('id');

        foreach ($languageIds as $languageId) {
            $row = DB::table('systems')
                ->where(['keyword' => 'homepage_logo', 'language_id' => $languageId]);

            // Tách update/insert thay vì updateOrInsert để không ghi đè created_at
            // của bản ghi cũ, và để created_at của bản ghi mới không bị null.
            if ($row->exists()) {
                $row->update(['content' => self::LOGO_PATH, 'updated_at' => now()]);
                continue;
            }

            DB::table('systems')->insert([
                'keyword' => 'homepage_logo',
                'language_id' => $languageId,
                'user_id' => $userId,
                'content' => self::LOGO_PATH,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info(
            'Đã đặt logo: ' . self::LOGO_PATH
            . ' cho language_id ' . $languageIds->implode(', ')
        );
    }
}
