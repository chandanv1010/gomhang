<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Nạp ghi chú giá hiển thị dưới khối ảnh + thông tin ở trang chi tiết sản phẩm.
 *
 * Khoá `product_note` nằm trong Admin -> Cấu hình -> "Cấu hình trang sản phẩm",
 * ô có trình soạn thảo nên sau này sửa thẳng trong admin.
 *
 * Có seeder vì nội dung nằm ở DB chứ không ở code: chỉ `git pull` thì trên host
 * khoá vẫn rỗng và khối ghi chú bị ẩn, nhìn như chưa làm gì.
 *
 * Chạy lại nhiều lần được: đã có nội dung thì bỏ qua, không đè bản người dùng
 * đã tự sửa. Muốn ép ghi đè thì xoá nội dung trong admin rồi chạy lại.
 */
class ProductNoteSeeder extends Seeder
{
    private const KEYWORD = 'product_note';

    private const NOI_DUNG = '<p><strong>Lưu ý:</strong> Mức giá trên chỉ mang tính tham khảo, '
        . 'áp dụng cho khách hàng mua lẻ. Khách sỉ, đại lý và cửa hàng vui lòng liên hệ Hotline '
        . '<a href="tel:0862542394"><strong>0862 542 394</strong></a> '
        . 'để được tư vấn, báo giá sỉ và hỗ trợ nhanh nhất.</p>';

    public function run(): void
    {
        $userId = DB::table('users')->whereNull('deleted_at')->orderBy('id')->value('id');
        $languageIds = DB::table('languages')->whereNull('deleted_at')->pluck('id');

        if ($languageIds->isEmpty()) {
            $languageIds = collect([1]);
        }

        $them = $boQua = 0;

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
                    'content' => self::NOI_DUNG,
                    'language_id' => $languageId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('systems')
                    ->where('keyword', self::KEYWORD)
                    ->where('language_id', $languageId)
                    ->update(['content' => self::NOI_DUNG, 'updated_at' => now()]);
            }

            $them++;
        }

        $this->command->info("Ghi chú trang sản phẩm: nạp {$them}, bỏ qua {$boQua} (đã có nội dung).");
    }
}
