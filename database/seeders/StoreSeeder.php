<?php

namespace Database\Seeders;

use App\Models\AddonGroup;
use App\Models\AddonOption;
use App\Models\Area;
use App\Models\Category;
use App\Models\Governorate;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Filament Admin User
        User::updateOrCreate(
            ['email' => 'admin@urcookies.com'],
            [
                'name' => 'Admin',
                'phone' => '66109161',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Kuwait Governorates & Areas
        $locations = [
            'Ahmadi' => ['Abu Halifa', 'Fahaheel', 'Mangaf', 'Mahboula'],
            'Mubarak Al-Kabeer' => ['Sabah Al-Salem', 'Al-Qurain'],
            'Farwaniya' => ['Khaitan', 'Farwaniya', 'Al-Rai'],
            'Hawalli' => ['Hawalli', 'Salmiya', 'Jabriya'],
            'Jahra' => ['Al-Jahra', 'Saad Al-Abdullah'],
            'Kuwait City' => ['Sharq', 'Mirgab', 'Dasman'],
        ];

        foreach ($locations as $govName => $areas) {
            $gov = Governorate::firstOrCreate(['name_en' => $govName]);
            foreach ($areas as $areaName) {
                Area::firstOrCreate(
                    ['governorate_id' => $gov->id, 'name_en' => $areaName],
                    ['delivery_fee' => 0.950]
                );
            }
        }

        // 3. Pickup Store
        Store::updateOrCreate(
            ['name' => 'Sharq'],
            [
                'location_description' => 'Kuwait City - Sharq Branch',
                'is_active' => true,
            ]
        );

        // =========================================================================
        // 4. CATEGORY 1: BEST SELLERS
        // =========================================================================
        $catBestSellers = Category::updateOrCreate(['slug' => 'best-sellers'], [
            'name' => 'BEST SELLERS',
            'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=600',
        ]);

        // Product WITHOUT Addon: Fixed price
        Product::updateOrCreate(['slug' => 'ur-bites-bestseller'], [
            'category_id' => $catBestSellers->id,
            'name' => 'UR Bites',
            'description' => 'Ice cream bites filled with vanilla ice cream and cookie dough, covered with double chocolate.',
            'image' => 'https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?w=500',
            'base_price' => 2.650, // Fixed price (no add-on needed)
        ]);

        // Product WITHOUT Addon: Fixed price
        Product::updateOrCreate(['slug' => 'ur-molten-bestseller'], [
            'category_id' => $catBestSellers->id,
            'name' => 'UR Molten 4 Pcs',
            'description' => 'Chocolate sauce filled in cup and baked with our signature cookies dough.',
            'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=500',
            'base_price' => 5.500, // Fixed price
        ]);

        // =========================================================================
        // 5. CATEGORY 2: UR CRISPY
        // =========================================================================
        $catCrispy = Category::updateOrCreate(['slug' => 'ur-crispy'], [
            'name' => 'UR Crispy',
            'image' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?w=600',
        ]);

        // Product WITHOUT Addon: Fixed price
        Product::updateOrCreate(['slug' => 'ur-cookie-crispy-cup'], [
            'category_id' => $catCrispy->id,
            'name' => 'UR Cookie Crispy Cup',
            'description' => 'Crunchy crispies with rich chocolate, topped with a UR cookie.',
            'image' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?w=500',
            'base_price' => 2.450, // Fixed price
        ]);

        // Product WITHOUT Addon: Fixed price
        Product::updateOrCreate(['slug' => 'crispy-cup'], [
            'category_id' => $catCrispy->id,
            'name' => 'Crispy Cup',
            'description' => 'Crunchy crispies covered with rich chocolate.',
            'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?w=500',
            'base_price' => 1.950, // Fixed price
        ]);

        // =========================================================================
        // 6. CATEGORY 3: GOOD FOR 2
        // =========================================================================
        $catGoodFor2 = Category::updateOrCreate(['slug' => 'good-for-2'], [
            'name' => 'GOOD FOR 2',
            'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600',
        ]);

        // Product WITHOUT Addon: Fixed price
        Product::updateOrCreate(['slug' => 'ur-molten-2-pcs'], [
            'category_id' => $catGoodFor2->id,
            'name' => 'UR Molten 2 Pcs',
            'description' => 'Chocolate sauce filled in cup and baked with our signature cookies dough.',
            'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=500',
            'base_price' => 3.450, // Fixed price
        ]);

        // Product WITHOUT Addon: Fixed price
        Product::updateOrCreate(['slug' => 'ur-tacos-2-pcs'], [
            'category_id' => $catGoodFor2->id,
            'name' => 'UR Tacos 4 Pcs',
            'description' => '4 Pieces of cookie taco filled with ice cream and dipped in rich chocolate sauce.',
            'image' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=500',
            'base_price' => 2.950, // Fixed price
        ]);

        // =========================================================================
        // 7. CATEGORY 4: COOKIES (MIX OF PRODUCTS WITH & WITHOUT ADDONS)
        // =========================================================================
        $catCookies = Category::updateOrCreate(['slug' => 'cookies'], [
            'name' => 'COOKIES',
            'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=600',
        ]);

        // ITEM A: Product WITH Addons (Required size + Optional extras)
        $milkCookies = Product::updateOrCreate(['slug' => 'milk-cookies-4'], [
            'category_id' => $catCookies->id,
            'name' => 'Milk Cookies',
            'description' => 'Freshly baked warm milk chocolate cookies.',
            'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=500',
            'base_price' => null, // "Price on selection"
        ]);

        // 1. Required Selection Group (Size)
        $groupSize = AddonGroup::updateOrCreate(
            ['product_id' => $milkCookies->id, 'name' => 'MILK COOKIES'],
            ['type' => 'radio', 'is_required' => true, 'max_selectable' => 1]
        );
        AddonOption::updateOrCreate(['addon_group_id' => $groupSize->id, 'name' => 'MILK COOKIES 6 PCS'], ['price' => 3.750]);
        AddonOption::updateOrCreate(['addon_group_id' => $groupSize->id, 'name' => 'MILK COOKIES 12 PCS'], ['price' => 6.250]);

        // 2. Optional Selection Group (Add-ons)
        $groupAddon = AddonGroup::updateOrCreate(
            ['product_id' => $milkCookies->id, 'name' => 'ADD ON'],
            ['type' => 'checkbox', 'is_required' => false, 'max_selectable' => 1]
        );
        AddonOption::updateOrCreate(['addon_group_id' => $groupAddon->id, 'name' => 'CRISPY COOKIES 3 PCS'], ['price' => 1.950]);
        AddonOption::updateOrCreate(['addon_group_id' => $groupAddon->id, 'name' => 'MILK COOKIES - 1 PIECE'], ['price' => 0.550]);
        AddonOption::updateOrCreate(['addon_group_id' => $groupAddon->id, 'name' => 'MATCHA COOKIES 3 PCS'], ['price' => 1.950]);
        AddonOption::updateOrCreate(['addon_group_id' => $groupAddon->id, 'name' => 'MILK CHOCOLATE COOKIES 3 PCS'], ['price' => 1.950]);

        // ITEM B: Another Product WITH Addons (Crispy Cookies)
        $crispyCookies = Product::updateOrCreate(['slug' => 'crispy-cookies'], [
            'category_id' => $catCookies->id,
            'name' => 'Crispy Cookies',
            'description' => 'Crispy thin caramelized edge cookies.',
            'image' => 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=500',
            'base_price' => null, // "Price on selection"
        ]);

        $crispySizeGroup = AddonGroup::updateOrCreate(
            ['product_id' => $crispyCookies->id, 'name' => 'CRISPY COOKIES BOX'],
            ['type' => 'radio', 'is_required' => true, 'max_selectable' => 1]
        );
        AddonOption::updateOrCreate(['addon_group_id' => $crispySizeGroup->id, 'name' => 'CRISPY COOKIES 6 PCS'], ['price' => 3.750]);
        AddonOption::updateOrCreate(['addon_group_id' => $crispySizeGroup->id, 'name' => 'CRISPY COOKIES 12 PCS'], ['price' => 6.250]);

        // ITEM C: Product WITHOUT Addons (Fixed price, direct add)
        Product::updateOrCreate(['slug' => 'mix-cookies-6-pcs'], [
            'category_id' => $catCookies->id,
            'name' => 'Mix Cookies 6 Pcs',
            'description' => '6 Cookies of our most loved flavors: Perfect for sharing and gathering.',
            'image' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=500',
            'base_price' => 3.750, // Fixed price (no add-ons)
        ]);

        // ITEM D: Product WITHOUT Addons (Fixed price, direct add)
        Product::updateOrCreate(['slug' => 'zwara-box'], [
            'category_id' => $catCookies->id,
            'name' => 'Zwara Box (24 Pcs)',
            'description' => '24 Cookies of our most loved flavors: Perfect for gatherings.',
            'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500',
            'base_price' => 10.950, // Fixed price (no add-ons)
        ]);
    }
}
