<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('admission_school', function (Blueprint $table) {
            $table->unsignedBigInteger('admission_id');
            $table->unsignedBigInteger('school_id');
            $table->foreign('admission_id')->references('id')->on('admissions')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('admission_school');
    }
};
