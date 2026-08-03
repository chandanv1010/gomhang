<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scholar_catalogue_id');
            $table->foreign('scholar_catalogue_id')->references('id')->on('scholar_catalogues')->onDelete('cascade'); 
            $table->unsignedBigInteger('policy_id');
            $table->foreign('policy_id')->references('id')->on('scholar_policies')->onDelete('cascade'); 
            $table->unsignedBigInteger('train_id');
            $table->foreign('train_id')->references('id')->on('scholar_trains')->onDelete('cascade'); 
            $table->longText('scholar_policy')->nullable();
            $table->text('album')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('scholars');
    }
};
