<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Tạo slide "commit-slides" cho khối 4 ảnh cam kết ở cuối trang sản phẩm.
 *
 * Bốn đường dẫn ảnh này vốn ghi thẳng trong view nên không sửa được từ admin.
 * Seeder nạp đúng 4 ảnh đang dùng vào slide để giao diện không đổi, sau đó
 * quản lý hoàn toàn ở Admin -> Slide -> commit-slides.
 *
 * Chạy được nhiều lần: đã có bản ghi thì bỏ qua, không đè lên ảnh người dùng
 * đã tự đổi.
 */
class CommitSlideSeeder extends Seeder
{
    private const KEYWORD = 'commit-slides';

    private const DEFAULT_ITEMS = [
        ['image' => '/userfiles/image/commit/giao-hang-toan-quoc-2.jpg', 'alt' => 'Giao hàng toàn quốc'],
        ['image' => '/userfiles/image/commit/8-ngay-doi-tra.webp', 'alt' => '8 ngày đổi trả'],
        ['image' => '/userfiles/image/commit/bao-hanh-1-2-nam.jpg', 'alt' => 'Bảo hành 1-2 năm'],
        ['image' => '/userfiles/image/commit/support-tron-doi.jpg', 'alt' => 'Support trọn đời'],
    ];

    public function run(): void
    {
        if (DB::table('slides')->where('keyword', self::KEYWORD)->exists()) {
            $this->command->info('Slide "' . self::KEYWORD . '" đã có, bỏ qua để không đè dữ liệu.');
            return;
        }

        $languageIds = DB::table('languages')->whereNull('deleted_at')->pluck('id');
        if ($languageIds->isEmpty()) {
            $languageIds = collect([1]);
        }

        // Cấu trúc item: { "<language_id>": [ {...}, {...} ] }
        $item = [];
        foreach ($languageIds as $languageId) {
            $item[(string) $languageId] = array_map(fn ($row) => [
                'image' => $row['image'],
                'name' => $row['alt'],
                'description' => null,
                'canonical' => null,
                'alt' => $row['alt'],
                'window' => '',
            ], self::DEFAULT_ITEMS);
        }

        DB::table('slides')->insert([
            'name' => 'Ảnh cam kết trang sản phẩm',
            'keyword' => self::KEYWORD,
            'item' => json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'publish' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info(
            'Đã tạo slide "' . self::KEYWORD . '" với ' . count(self::DEFAULT_ITEMS)
            . ' ảnh cho language_id ' . $languageIds->implode(', ')
        );
    }
}
