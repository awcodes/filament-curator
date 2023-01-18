<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->string('path')->nullable();
            $table->longText('curations')->nullable();
        });
    }

    public function down()
    {
        Schema::table('media', function(Blueprint $table) {
            $table->dropColumn(['name', 'path', 'curations']);
        });
    }
};
