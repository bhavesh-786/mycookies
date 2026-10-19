<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add missing fields to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'floor')) {
                $table->string('floor')->nullable()->after('building');
            }
            if (!Schema::hasColumn('orders', 'flat_no')) {
                $table->string('flat_no')->nullable()->after('floor');
            }
        });

        // 2. Add notes to order_items table for per-product remarks
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'notes')) {
                $table->text('notes')->nullable()->after('selected_addons');
            }
        });

        // 3. Create persistent Database-level Address Table for customers
        if (!Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id')->index();
                $table->string('type')->default('Home'); // Home, Office, Friend
                $table->unsignedBigInteger('area_id')->nullable();
                $table->string('area_name')->nullable();
                $table->string('block');
                $table->string('street');
                $table->string('avenue')->nullable();
                $table->string('building');
                $table->string('floor')->nullable();
                $table->string('flat_no')->nullable();
                $table->string('additional_directions')->nullable();
                $table->string('paci')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['notes']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['floor', 'flat_no']);
        });
    }
};
