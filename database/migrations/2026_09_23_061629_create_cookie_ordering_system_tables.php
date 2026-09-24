<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Kuwait Governorates & Areas
        Schema::create('governorates', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('governorate_id')->constrained()->cascadeOnDelete();
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->decimal('delivery_fee', 8, 3)->default(0.950);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Pickup Branches/Stores
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Categories & Products
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('base_price', 8, 3)->nullable(); // null if 'Price on selection'
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Product Add-on Groups & Items (e.g., Size variation, extra toppings)
        Schema::create('addon_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "MILK COOKIES", "ADD ON"
            $table->string('type')->default('radio'); // 'radio' (single choice) or 'checkbox' (multiple)
            $table->boolean('is_required')->default(false);
            $table->integer('min_selectable')->default(0);
            $table->integer('max_selectable')->default(1);
            $table->timestamps();
        });

        Schema::create('addon_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_group_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "MILK COOKIES 6 PCS"
            $table->decimal('price', 8, 3)->default(0.000);
            $table->timestamps();
        });

        // 5. Orders & Order Items
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_type'); // 'delivery' or 'pickup'
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // Address details for Kuwait delivery
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('address_type')->nullable(); // Home, Apartment, Office
            $table->string('block')->nullable();
            $table->string('street')->nullable();
            $table->string('building_or_house')->nullable();
            $table->string('avenue')->nullable();
            $table->string('paci_number')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('payment_method'); // 'cash', 'debit_card' (KNET), 'credit_card'
            $table->decimal('subtotal', 8, 3);
            $table->decimal('delivery_fee', 8, 3)->default(0.000);
            $table->decimal('total', 8, 3);
            $table->string('status')->default('pending'); // pending, confirmed, out_for_delivery, completed
            $table->text('special_remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 3);
            $table->decimal('total_price', 8, 3);
            $table->json('selected_addons')->nullable(); // Store chosen add-on names & prices snapshot
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('addon_options');
        Schema::dropIfExists('addon_groups');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('stores');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('governorates');
    }
};
