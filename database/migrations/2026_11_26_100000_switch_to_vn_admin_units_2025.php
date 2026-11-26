<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Chuyen dia gioi hanh chinh sang bo sau sap nhap (hieu luc 01/07/2025):
 * 34 tinh/thanh, 3.321 phuong/xa/dac khu, chi con 2 cap (khong con quan/huyen).
 *
 * Bang cu 63 tinh / 705 huyen / 10.598 xa duoc doi ten sang *_legacy chu khong
 * xoa, de down() tra lai duoc. Bang districts giu nguyen (khong con dung).
 * Nguon du lieu: database/data/vn_units_2025.json (lay tu provinces.open-api.vn v2,
 * ma theo danh muc cua Tong cuc Thong ke).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('provinces') && !Schema::hasTable('provinces_legacy')) {
            Schema::rename('provinces', 'provinces_legacy');
        }
        if (Schema::hasTable('wards') && !Schema::hasTable('wards_legacy')) {
            Schema::rename('wards', 'wards_legacy');
        }
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('wards');

        Schema::create('provinces', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('name');
            $table->string('full_name');
            $table->string('code_name')->nullable();
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->string('code', 20)->primary();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('code_name')->nullable();
            $table->string('province_code', 20)->index();
            // Giu cot cho code cu con tham chieu; bo sau sap nhap khong co cap huyen.
            $table->string('district_code', 20)->nullable();
        });

        $data = json_decode(file_get_contents(database_path('data/vn_units_2025.json')), true);

        foreach ($data as $province) {
            DB::table('provinces')->insert([
                'code' => $province['code'],
                'name' => $province['name'],
                'full_name' => $province['full_name'],
                'code_name' => $province['code_name'],
            ]);

            $rows = array_map(fn ($w) => [
                'code' => $w[0],
                'name' => $w[1],
                'full_name' => $w[1],
                'code_name' => $w[2],
                'province_code' => $province['code'],
                'district_code' => null,
            ], $province['wards']);

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('wards')->insert($chunk);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('wards');

        if (Schema::hasTable('provinces_legacy')) {
            Schema::rename('provinces_legacy', 'provinces');
        }
        if (Schema::hasTable('wards_legacy')) {
            Schema::rename('wards_legacy', 'wards');
        }
    }
};
