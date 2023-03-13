<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (! Schema::hasColumn('media', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('media', 'path')) {
                $table->string('path')->nullable();
            }

            if (! Schema::hasColumn('media', 'curations')) {
                $table->longText('curations')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('media', function(Blueprint $table) {
            $table->dropColumn(['name', 'path', 'curations']);
        });
    }
};
