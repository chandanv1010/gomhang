<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholar_school', function (Blueprint $table) {
            $table->unsignedBigInteger('scholar_id');
            $table->unsignedBigInteger('school_id');
            $table->foreign('scholar_id')->references('id')->on('scholars')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholar_school');
    }
};
