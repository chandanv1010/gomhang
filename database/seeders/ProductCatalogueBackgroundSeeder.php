<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCatalogue;

class ProductCatalogueBackgroundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colorMappings = [
            2 => '#006D3A',  // Ống dẫn nước Inox không hàn (Green)
            3 => '#b31f24',  // Ống PCCC Thép Carbon không hàn (Red)
            7 => '#006D3A',  // Phụ kiện ống nước Inox (Green)
            9 => '#b31f24',  // Phụ kiện ống PCCC Carbon (Red)
            10 => '#1e4794', // Van hệ thống nước (Blue)
            11 => '#6b7280', // Van công nghiệp (Grey)
            12 => '#b31f24', // Van chữa cháy (Red)
            13 => '#eab308', // Van đồng (Yellow)
        ];

        foreach ($colorMappings as $id => $color) {
            ProductCatalogue::where('id', $id)->update(['background' => $color]);
        }
    }
}
