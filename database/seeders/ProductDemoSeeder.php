<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductDemoSeeder extends Seeder
{
    public function run()
    {
        // Clean existing tables to avoid duplicates
        DB::table('product_catalogue_product')->truncate();
        DB::table('product_language')->truncate();
        DB::table('routers')->where('controllers', '=', 'App\Http\Controllers\Frontend\ProductController')->delete();
        Product::query()->forceDelete();

        // 24 realistic products distributed across 4 categories
        $categories = [
            2 => [ // Vòi Lavabo
                'name' => 'Vòi Lavabo',
                'image' => '/vendor/frontend/img/project/tazen/product_voi_lavabo.png',
                'items' => [
                    ['name' => 'Vòi lavabo nóng lạnh cao cấp Tazen', 'price' => 1200000],
                    ['name' => 'Vòi lavabo âm tường hiện đại', 'price' => 1850000],
                    ['name' => 'Vòi lavabo cổ ngỗng sang trọng', 'price' => 2100000],
                    ['name' => 'Vòi lavabo cảm ứng thông minh', 'price' => 3500000],
                    ['name' => 'Vòi lavabo mạ vàng cổ điển', 'price' => 4200000],
                    ['name' => 'Vòi lavabo đen mờ tối giản', 'price' => 1600000],
                ]
            ],
            3 => [ // Vòi Sen Tắm
                'name' => 'Vòi Sen Tắm',
                'image' => '/vendor/frontend/img/project/tazen/product_voi_sen_tam.png',
                'items' => [
                    ['name' => 'Vòi sen tắm nóng lạnh Inox 304', 'price' => 950000],
                    ['name' => 'Vòi sen tắm đứng bát tròn Tazen', 'price' => 1450000],
                    ['name' => 'Vòi sen tắm âm tường cao cấp', 'price' => 3200000],
                    ['name' => 'Vòi sen tắm massage đa năng', 'price' => 2400000],
                    ['name' => 'Vòi sen tắm cầm tay tăng áp', 'price' => 750000],
                    ['name' => 'Vòi sen tắm nhiệt độ tự động', 'price' => 4800000],
                ]
            ],
            4 => [ // Cây Sen Tắm
                'name' => 'Cây Sen Tắm',
                'image' => '/vendor/frontend/img/project/tazen/product_cay_sen_tam.png',
                'items' => [
                    ['name' => 'Cây sen tắm nóng lạnh bát tròn', 'price' => 2900000],
                    ['name' => 'Cây sen tắm phím đàn hiển thị nhiệt độ', 'price' => 4500000],
                    ['name' => 'Cây sen tắm Inox 304 đen mờ', 'price' => 3800000],
                    ['name' => 'Cây sen tắm âm trần hiện đại', 'price' => 8500000],
                    ['name' => 'Cây sen tắm mạ chrome bóng', 'price' => 3200000],
                    ['name' => 'Cây sen tắm thông minh cảm ứng', 'price' => 12000000],
                ]
            ],
            5 => [ // Phụ Kiện
                'name' => 'Phụ Kiện',
                'image' => '/vendor/frontend/img/project/tazen/product_phu_kien.png',
                'items' => [
                    ['name' => 'Kệ kính phòng tắm cao cấp', 'price' => 350000],
                    ['name' => 'Móc treo khăn Inox 304', 'price' => 25000],
                    ['name' => 'Lô giấy vệ sinh âm tường', 'price' => 450000],
                    ['name' => 'Hộp đựng nước rửa tay sứ', 'price' => 300000],
                    ['name' => 'Thoát sàn ngăn mùi thông minh', 'price' => 280000],
                    ['name' => 'Gương led cảm ứng phòng tắm', 'price' => 1950000],
                ]
            ]
        ];

        $userId = 1; // Default admin user ID or similar

        $orderIndex = 1;
        foreach ($categories as $catId => $catData) {
            foreach ($catData['items'] as $item) {
                $slug = Str::slug($item['name']);
                
                // Create product
                $productId = DB::table('products')->insertGetId([
                    'image' => $catData['image'],
                    'publish' => 2,
                    'follow' => 1,
                    'order' => $orderIndex++,
                    'user_id' => $userId,
                    'product_catalogue_id' => $catId,
                    'price' => $item['price'],
                    'stock' => 100,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create translation for VN language (language_id = 1)
                DB::table('product_language')->insert([
                    'product_id' => $productId,
                    'language_id' => 1,
                    'name' => $item['name'],
                    'canonical' => $slug,
                    'description' => "Sản phẩm {$item['name']} được làm từ chất liệu cao cấp, độ bền cao, bảo hành dài hạn.",
                    'content' => "<p><strong>{$item['name']}</strong> là sự lựa chọn hoàn hảo cho phòng tắm gia đình bạn. Được thiết kế tinh xảo, hiện đại, mang lại không gian sang trọng và tiện nghi bậc nhất.</p><p>Chi tiết sản phẩm:</p><ul><li>Chất liệu bền bỉ vượt trội</li><li>Dễ dàng lắp đặt và bảo dưỡng</li><li>Bảo hành chính hãng 3 năm</li></ul>",
                    'meta_title' => $item['name'],
                    'meta_description' => "Mua ngay {$item['name']} chất lượng cao tại Tazen.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create category relation
                DB::table('product_catalogue_product')->insert([
                    'product_id' => $productId,
                    'product_catalogue_id' => $catId,
                ]);

                // Create router record
                DB::table('routers')->insert([
                    'canonical' => $slug,
                    'module_id' => $productId,
                    'language_id' => 1,
                    'controllers' => 'App\Http\Controllers\Frontend\ProductController',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Seeded 24 demo products successfully!');
    }
}
