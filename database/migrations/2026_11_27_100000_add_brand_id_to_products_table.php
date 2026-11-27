<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thương hiệu của sản phẩm (id thuộc tính nhóm "Thương hiệu"). Trước đây chỉ
 * gắn được qua khối phiên bản, cách đó sinh phiên bản theo thương hiệu.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('products', 'brand_id')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('products', 'brand_id')) {
            return;
        }
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
};
