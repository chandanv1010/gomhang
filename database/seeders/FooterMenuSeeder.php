<?php

namespace Database\Seeders;

use App\Classes\Nestedsetbie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dựng lại "Menu chân trang" (footer-menu).
 *
 * Mỗi mục cấp 1 là một CỘT bên phải footer, các mục con là link trong cột đó.
 * Trước đây các cột này ghi cứng trong footer.blade.php nên không thêm bớt được.
 *
 * Menu cũ là của VIREX (ống nước, van, PCCC) và đã ẩn đi ở bước dọn lỗi phông.
 * Chạy lại nhiều lần được: mỗi lần dựng lại từ đầu.
 */
class FooterMenuSeeder extends Seeder
{
    private const KEYWORD = 'footer-menu';

    /** @var array<int, array{name: string, children: array<int, array{name: string, canonical: string}>}> */
    private const MENU = [
        [
            'name' => 'Chính sách & Hỗ trợ',
            'children' => [
                ['name' => 'Chính sách vận chuyển', 'canonical' => 'chinh-sach-van-chuyen'],
                ['name' => 'Chính sách bảo mật', 'canonical' => 'chinh-sach-bao-mat'],
                ['name' => 'Chính sách bảo hành', 'canonical' => 'chinh-sach-bao-hanh'],
                ['name' => 'Địa chỉ shop', 'canonical' => 'dia-chi-shop'],
            ],
        ],
        [
            'name' => 'Danh mục',
            'children' => [
                ['name' => 'Phụ kiện theo chủng loại', 'canonical' => 'phu-kien-theo-chung-loai'],
                ['name' => 'Phụ kiện iPhone', 'canonical' => 'phu-kien-iphone'],
                ['name' => 'Phụ kiện Samsung', 'canonical' => 'phu-kien-samsung'],
                ['name' => 'Kiến thức', 'canonical' => 'tin-tuc-gomhang-vn'],
            ],
        ],
    ];

    public function run(): void
    {
        $catalogue = DB::table('menu_catalogues')
            ->where('keyword', self::KEYWORD)
            ->whereNull('deleted_at')
            ->first();

        if (!$catalogue) {
            $this->command->error('Không tìm thấy nhóm menu "' . self::KEYWORD . '".');
            return;
        }

        $userId = DB::table('users')->whereNull('deleted_at')->orderBy('id')->value('id');
        $languageIds = DB::table('languages')->whereNull('deleted_at')->pluck('id');
        if ($languageIds->isEmpty()) {
            $languageIds = collect([1]);
        }

        DB::transaction(function () use ($catalogue, $userId, $languageIds) {
            $cu = DB::table('menus')
                ->where('menu_catalogue_id', $catalogue->id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);

            $this->command->info("Đã ẩn {$cu} mục menu chân trang cũ.");

            // MenuComposer sắp `order` giảm dần nên đánh số lùi để đọc xuôi.
            $order = count(self::MENU);

            foreach (self::MENU as $cot) {
                $parentId = $this->themMuc(
                    ['name' => $cot['name'], 'canonical' => ''],
                    0, $order--, $catalogue->id, $userId, $languageIds
                );

                $orderCon = count($cot['children']);
                foreach ($cot['children'] as $link) {
                    $this->themMuc($link, $parentId, $orderCon--, $catalogue->id, $userId, $languageIds);
                }
            }
        });

        $nestedset = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => true,
            'language_id' => (int) $languageIds->first(),
        ]);
        $nestedset->Get('level ASC, order ASC');
        $nestedset->Recursive(0, $nestedset->Set());
        $nestedset->Action();

        $soCot = count(self::MENU);
        $soLink = array_sum(array_map(fn ($c) => count($c['children']), self::MENU));
        $this->command->info("Đã dựng lại menu chân trang: {$soCot} cột, {$soLink} link.");
    }

    private function themMuc(
        array $muc,
        int $parentId,
        int $order,
        int $catalogueId,
        ?int $userId,
        $languageIds
    ): int {
        $menuId = DB::table('menus')->insertGetId([
            'parent_id' => $parentId,
            'menu_catalogue_id' => $catalogueId,
            'level' => $parentId === 0 ? 0 : 1,
            'order' => $order,
            'publish' => 2,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($languageIds as $languageId) {
            DB::table('menu_language')->insert([
                'menu_id' => $menuId,
                'language_id' => $languageId,
                'name' => $muc['name'],
                'canonical' => $muc['canonical'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $menuId;
    }
}
