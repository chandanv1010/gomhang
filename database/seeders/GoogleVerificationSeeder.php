<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Nạp mã xác minh Google Search Console vào khoá `seo_google_verification`.
 *
 * Ô nhập nằm ở Admin -> Cấu hình -> "Cấu hình SEO dành cho trang chủ", nên sau
 * này đổi mã thì sửa thẳng trong admin, không cần seeder nữa.
 *
 * Có seeder vì giá trị nằm ở DB chứ không ở code: chỉ `git pull` trên host thì
 * khoá vẫn rỗng, thẻ meta không xuất ra và Google báo xác minh thất bại.
 *
 * Chạy lại nhiều lần được: đã có mã thì bỏ qua, không đè giá trị người dùng đã
 * tự nhập trong admin.
 *
 * Lưu ý: đây chỉ là cách xác minh thứ hai. Cách thứ nhất là tệp
 * public/google63ed55b025dc319c.html - tệp tĩnh, không phụ thuộc DB.
 * Bật cả hai để nếu một cách lỗi thì cách kia vẫn xác minh được.
 */
class GoogleVerificationSeeder extends Seeder
{
    private const KEYWORD = 'seo_google_verification';

    private const MA = '54GJA3kPjeE9Ph4OSgx5-sQrVAXYDayJlH-R6RpnlJc';

    public function run(): void
    {
        $userId = DB::table('users')->whereNull('deleted_at')->orderBy('id')->value('id');
        $languageIds = DB::table('languages')->whereNull('deleted_at')->pluck('id');

        if ($languageIds->isEmpty()) {
            $languageIds = collect([1]);
        }

        $them = $capNhat = $boQua = 0;

        foreach ($languageIds as $languageId) {
            $hienTai = DB::table('systems')
                ->where('keyword', self::KEYWORD)
                ->where('language_id', $languageId)
                ->value('content');

            if (trim((string) $hienTai) !== '') {
                $boQua++;
                continue;
            }

            if ($hienTai === null) {
                DB::table('systems')->insert([
                    'keyword' => self::KEYWORD,
                    'content' => self::MA,
                    'language_id' => $languageId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $them++;
                continue;
            }

            DB::table('systems')
                ->where('keyword', self::KEYWORD)
                ->where('language_id', $languageId)
                ->update([
                    'content' => self::MA,
                    'user_id' => $userId,
                    'updated_at' => now(),
                ]);
            $capNhat++;
        }

        $this->command->info(
            "Mã xác minh Google: thêm {$them}, cập nhật {$capNhat}, bỏ qua {$boQua} (đã có sẵn)."
        );
    }
}
