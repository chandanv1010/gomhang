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
        Schema::table('products', function (Blueprint $table) {
             $table->dropForeign(['lecturer_id']); 
             $table->dropColumn('lecturer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Nếu rollback, thêm lại cột và ràng buộc
            $table->unsignedBigInteger('lecturer_id')->nullable()->after('iframe');

            $table->foreign('lecturer_id')
                ->references('id')
                ->on('lecturers')
                ->onDelete('set null');
        });
    }
};
