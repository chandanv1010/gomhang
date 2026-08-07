<?php

namespace Database\Seeders;

use App\Classes\Nestedsetbie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dựng lại "Menu chính" (main-menu) cho đúng ngành hàng của website.
 *
 * Menu cũ trong admin là của VIREX - "Ống PCCC Thép Carbon không hàn",
 * "Van chữa cháy", "Van công nghiệp" - tức ngành hàng khác hẳn, lại còn bị lỗi
 * phông (13/14 dòng thành "Li??n H???"). Header thì ghi cứng nên trước giờ
 * không ai thấy đống này.
 *
 * Seeder xoá mềm menu cũ của nhóm main-menu rồi tạo lại đúng 4 mục + 3 mục con.
 * Chạy lại nhiều lần được: mỗi lần dựng lại từ đầu.
 */
class MainMenuSeeder extends Seeder
{
    private const KEYWORD = 'main-menu';

    /**
     * canonical rỗng = trang chủ. View tự đổi thành "/".
     *
     * @var array<int, array{name: string, canonical: string, children?: array}>
     */
    private const MENU = [
        ['name' => 'Trang chủ', 'canonical' => ''],
        [
            'name' => 'Sản phẩm',
            'canonical' => 'phu-kien-dien-thoai',
            'children' => [
                ['name' => 'Phụ kiện theo chủng loại', 'canonical' => 'phu-kien-theo-chung-loai'],
                ['name' => 'Phụ kiện iPhone', 'canonical' => 'phu-kien-iphone'],
                ['name' => 'Phụ kiện Samsung', 'canonical' => 'phu-kien-samsung'],
            ],
        ],
        ['name' => 'Kiến Thức', 'canonical' => 'tin-tuc-gomhang-vn'],
        ['name' => 'Liên Hệ', 'canonical' => 'lien-he'],
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
            // Xoá mềm menu cũ để còn lấy lại được nếu cần đối chiếu.
            $cu = DB::table('menus')
                ->where('menu_catalogue_id', $catalogue->id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);

            $this->command->info("Đã ẩn {$cu} mục menu cũ.");

            // MenuComposer sắp xếp `order` GIẢM DẦN, nên mục hiện trước phải mang
            // số lớn hơn. Đánh số lùi để mảng self::MENU đọc theo đúng thứ tự
            // hiển thị từ trái sang phải.
            $order = count(self::MENU);

            foreach (self::MENU as $muc) {
                $parentId = $this->themMuc($muc, 0, $order--, $catalogue->id, $userId, $languageIds);

                $children = $muc['children'] ?? [];
                $orderCon = count($children);
                foreach ($children as $con) {
                    $this->themMuc($con, $parentId, $orderCon--, $catalogue->id, $userId, $languageIds);
                }
            }
        });

        // Dựng lại cây lft/rgt/level, nếu không menu con không lồng đúng cấp.
        // Bảng menus phải khai báo isMenu, không thì Nestedsetbie đi tìm bảng
        // menus_catalogue_language không tồn tại. Giống hệt MenuService::initialize().
        $nestedset = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => true,
            'language_id' => (int) $languageIds->first(),
        ]);
        $nestedset->Get('level ASC, order ASC');
        $nestedset->Recursive(0, $nestedset->Set());
        $nestedset->Action();

        $this->command->info('Đã dựng lại menu chính: 4 mục, 3 mục con.');
    }

    /** @return int id của mục vừa tạo */
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
