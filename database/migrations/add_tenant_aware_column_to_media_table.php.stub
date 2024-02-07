<?php

use Awcodes\Curator\Facades\Curator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if(config('curator.is_tenant_aware')) {
            Schema::table(app(config('curator.model'))->getTable(), function (Blueprint $table) {
                $table->integer(config('curator.tenant_ownership_relationship_name') . '_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn(app(config('curator.model'))->getTable(), config('curator.tenant_ownership_relationship_name') . '_id')) {
            Schema::table(app(config('curator.model'))->getTable(), function(Blueprint $table) {
                $table->dropColumn(config('curator.tenant_ownership_relationship_name') . '_id');
            });
        }
    }
};
