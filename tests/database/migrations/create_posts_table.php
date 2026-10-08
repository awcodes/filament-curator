<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->json('gallery')->nullable();
            $table->json('content')->nullable();
            $table->json('meta')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }
};
