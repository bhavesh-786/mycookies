<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories Table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });

        // 2. Products Table
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
            $table->text('description_ar')->nullable()->after('description');
        });

        // 3. Addon Groups Table
        Schema::table('addon_groups', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });

        // 4. Addon Options Table
        Schema::table('addon_options', function (Blueprint $table) {
            $table->string('name_ar')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('categories', fn(Blueprint $table) => $table->dropColumn('name_ar'));
        Schema::table('products', fn(Blueprint $table) => $table->dropColumn(['name_ar', 'description_ar']));
        Schema::table('addon_groups', fn(Blueprint $table) => $table->dropColumn('name_ar'));
        Schema::table('addon_options', fn(Blueprint $table) => $table->dropColumn('name_ar'));
    }
};
