<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_catalogues', function (Blueprint $table) {
            $table->float('point_percent')->default(0)->comment('Phần trăm điểm tích lũy khi mua hàng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_catalogues', function (Blueprint $table) {
            $table->dropColumn('point_percent');
        });
    }
};
