<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'area_name')) {
                $table->string('area_name')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('orders', 'address_type')) {
                $table->string('address_type')->nullable()->after('area_name');
            }
            if (!Schema::hasColumn('orders', 'block')) {
                $table->string('block')->nullable()->after('address_type');
            }
            if (!Schema::hasColumn('orders', 'street')) {
                $table->string('street')->nullable()->after('block');
            }
            if (!Schema::hasColumn('orders', 'building')) {
                $table->string('building')->nullable()->after('street');
            }
            if (!Schema::hasColumn('orders', 'avenue')) {
                $table->string('avenue')->nullable()->after('building');
            }
            if (!Schema::hasColumn('orders', 'paci')) {
                $table->string('paci')->nullable()->after('avenue');
            }
            if (!Schema::hasColumn('orders', 'special_remarks')) {
                $table->text('special_remarks')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
