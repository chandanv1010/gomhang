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
        Schema::table('product_catalogues', function (Blueprint $table) {
            $table->string('image_fit', 20)->default('cover')->after('background');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_catalogues', function (Blueprint $table) {
            $table->dropColumn('image_fit');
        });
    }
};
