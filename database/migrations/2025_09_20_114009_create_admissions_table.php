<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->datetime('submission_time')->nullable();
            $table->longText('admissions_info')->nullable();
            $table->string('image')->nullable();
            $table->text('album')->nullable();
            $table->unsignedBigInteger('admission_catalogue_id');
            $table->foreign('admission_catalogue_id')->references('id')->on('admission_catalogues')->onDelete('cascade');
            $table->unsignedBigInteger('scholar_id');
            $table->foreign('scholar_id')->references('id')->on('scholars')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->enum('publish', [1,2])->default(1);
            $table->integer('order')->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
