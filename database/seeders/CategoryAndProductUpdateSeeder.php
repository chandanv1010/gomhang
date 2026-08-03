<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryAndProductUpdateSeeder extends Seeder
{
    public function run()
    {
        // 1. Move products from category ID 7 (Phụ kiện ống nước Inox) to category ID 2 (Ống dẫn nước Inox không hàn)
        $inoxProducts = DB::table('products')->where('product_catalogue_id', 7)->get();
        foreach ($inoxProducts as $prod) {
            DB::table('products')->where('id', $prod->id)->update([
                'product_catalogue_id' => 2,
                'updated_at' => now(),
            ]);
            
            // Check if relationship exists in pivot table
            $exists = DB::table('product_catalogue_product')
                ->where('product_id', $prod->id)
                ->where('product_catalogue_id', 2)
                ->exists();
            if (!$exists) {
                // Remove old relation if exists
                DB::table('product_catalogue_product')
                    ->where('product_id', $prod->id)
                    ->where('product_catalogue_id', 7)
                    ->delete();
                // Insert new relation
                DB::table('product_catalogue_product')->insert([
                    'product_id' => $prod->id,
                    'product_catalogue_id' => 2,
                ]);
            } else {
                DB::table('product_catalogue_product')
                    ->where('product_id', $prod->id)
                    ->where('product_catalogue_id', 7)
                    ->delete();
            }
        }

        // 2. Move products from category ID 9 (Phụ kiện ống PCCC Carbon) to category ID 3 (Ống PCCC Thép Carbon không hàn)
        $pcccProducts = DB::table('products')->where('product_catalogue_id', 9)->get();
        foreach ($pcccProducts as $prod) {
            DB::table('products')->where('id', $prod->id)->update([
                'product_catalogue_id' => 3,
                'updated_at' => now(),
            ]);
            
            // Check if relationship exists in pivot table
            $exists = DB::table('product_catalogue_product')
                ->where('product_id', $prod->id)
                ->where('product_catalogue_id', 3)
                ->exists();
            if (!$exists) {
                // Remove old relation if exists
                DB::table('product_catalogue_product')
                    ->where('product_id', $prod->id)
                    ->where('product_catalogue_id', 9)
                    ->delete();
                // Insert new relation
                DB::table('product_catalogue_product')->insert([
                    'product_id' => $prod->id,
                    'product_catalogue_id' => 3,
                ]);
            } else {
                DB::table('product_catalogue_product')
                    ->where('product_id', $prod->id)
                    ->where('product_catalogue_id', 9)
                    ->delete();
            }
        }

        // 3. Delete redundant categories (ID 7 and ID 9)
        DB::table('product_catalogues')->whereIn('id', [7, 9])->delete();
        DB::table('product_catalogue_language')->whereIn('product_catalogue_id', [7, 9])->delete();
        DB::table('routers')->whereIn('module_id', [7, 9])->where('controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController')->delete();

        // 4. Update parent category options and order sorting:
        // Set image_fit = cover for categories 2 and 3 because they contain real background images (workers on site)
        DB::table('product_catalogues')->where('id', 2)->update([
            'image_fit' => 'cover',
            'order' => 1,
        ]);
        DB::table('product_catalogues')->where('id', 3)->update([
            'image_fit' => 'cover',
            'order' => 2,
        ]);

        // Sibling categories 10, 11, 12, 13 contain single object images on white backgrounds, so set them to contain
        DB::table('product_catalogues')->whereIn('id', [10, 11, 12, 13])->update([
            'image_fit' => 'contain',
        ]);

        // Push other sibling categories down
        DB::table('product_catalogues')->whereNotIn('id', [1, 2, 3])->where('parent_id', 1)->update([
            'order' => DB::raw('`id` + 10'),
        ]);

        // 5. Scan and copy images from work/anh_san_pham/ directories, and create/update demo products
        $this->syncProductsFromDir('01_Ong_PCCC', 3);
        $this->syncProductsFromDir('02_Ong_Inox', 2);

        // 6. Rebuild Nested Set boundaries for product_catalogues
        $nestedset = new \App\Classes\Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => 1,
        ]);
        $nestedset->Get();
        $array = $nestedset->Set();
        if (is_array($array)) {
            $nestedset->Recursive(0, $array);
            $nestedset->Action();
        }

        $this->command->info('Categories updated, merged, and demo products synced successfully!');
    }

    private function syncProductsFromDir($dirName, $targetCatId)
    {
        $dirPath = base_path('work/anh_san_pham/' . $dirName);
        if (!File::exists($dirPath)) {
            $this->command->warn("Directory not found: " . $dirPath);
            return;
        }

        $destDir = public_path('uploads/extracted_images');
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $files = File::files($dirPath);
        foreach ($files as $file) {
            $filenameWithExt = $file->getFilename();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            
            // Normalize name: Ba_chạc_ren_trong -> Ba chạc ren trong
            $normalizedName = str_replace('_', ' ', $filename);
            
            // Slugify filename for the destination file and canonical
            $slug = Str::slug($normalizedName);
            $destFilename = $slug . '.png';
            $destPath = $destDir . '/' . $destFilename;

            // Copy file to public/uploads/extracted_images/
            File::copy($file->getPathname(), $destPath);

            $imageRelativePath = '/uploads/extracted_images/' . $destFilename;

            // Check if product exists by name or canonical
            $existingProductLang = DB::table('product_language')
                ->where('language_id', 1)
                ->where(function($query) use ($normalizedName, $slug) {
                    $query->where('name', $normalizedName)
                          ->orWhere('canonical', $slug);
                })
                ->first();

            if ($existingProductLang) {
                // Product exists, update target category, image and canonical
                $productId = $existingProductLang->product_id;
                DB::table('products')->where('id', $productId)->update([
                    'product_catalogue_id' => $targetCatId,
                    'image' => $imageRelativePath,
                    'publish' => 2,
                    'updated_at' => now(),
                ]);

                // Ensure pivot table record is correct
                DB::table('product_catalogue_product')->where('product_id', $productId)->delete();
                DB::table('product_catalogue_product')->insert([
                    'product_id' => $productId,
                    'product_catalogue_id' => $targetCatId,
                ]);

                // Update translation
                DB::table('product_language')
                    ->where('product_id', $productId)
                    ->where('language_id', 1)
                    ->update([
                        'name' => $normalizedName,
                        'canonical' => $slug,
                        'updated_at' => now(),
                    ]);

                // Update router record
                DB::table('routers')
                    ->where('module_id', $productId)
                    ->where('controllers', 'App\Http\Controllers\Frontend\ProductController')
                    ->update([
                        'canonical' => $slug,
                        'updated_at' => now(),
                    ]);
            } else {
                // Product does not exist, insert new
                $productId = DB::table('products')->insertGetId([
                    'image' => $imageRelativePath,
                    'publish' => 2,
                    'follow' => 1,
                    'order' => 0,
                    'user_id' => 1, // Default user
                    'product_catalogue_id' => $targetCatId,
                    'price' => 150000,
                    'stock' => 100,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert translation
                DB::table('product_language')->insert([
                    'product_id' => $productId,
                    'language_id' => 1,
                    'name' => $normalizedName,
                    'canonical' => $slug,
                    'description' => "Sản phẩm {$normalizedName} được thiết kế chính xác từ vật liệu cao cấp, độ bền vượt trội.",
                    'content' => "<p><strong>{$normalizedName}</strong> là thiết bị phụ kiện chất lượng cao chuyên dùng cho các công trình hệ thống ống nước và PCCC. Sản phẩm bền bỉ, dễ lắp ráp và đạt tiêu chuẩn an toàn.</p>",
                    'meta_title' => $normalizedName,
                    'meta_description' => "Mua sản phẩm {$normalizedName} chất lượng cao chính hãng.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Insert pivot relation
                DB::table('product_catalogue_product')->insert([
                    'product_id' => $productId,
                    'product_catalogue_id' => $targetCatId,
                ]);

                // Insert router record
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
    }
}
