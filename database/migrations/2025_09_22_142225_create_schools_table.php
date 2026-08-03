<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id');
            $table->foreign('area_id')->references('id')->on('school_areas')->onDelete('cascade'); 
            $table->string('code')->nullable(); 
            $table->integer('rank')->nullable(); 
            $table->text('panorama')->nullable(); 
            $table->longText('information')->nullable(); 
            $table->text('video')->nullable(); 
            $table->text('album')->nullable(); 
            $table->string('image')->nullable();
            $table->string('logo')->nullable(); 
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('link_website')->nullable();
            $table->text('map')->nullable();
            $table->longText('training_major')->nullable();
            $table->longText('question_answer')->nullable();
            $table->integer('likes')->nullable();
            $table->integer('favorites')->nullable(); 
            $table->integer('view')->nullable();
            $table->enum('publish', [1,2])->default(1);
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); 
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
