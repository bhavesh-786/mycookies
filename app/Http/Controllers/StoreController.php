<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Governorate;
use App\Models\Area;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    public function index()
    {
        $isAr = app()->getLocale() === 'ar';

        // 1. Categories and Products with Addon Groups & Options
        $categories = Category::with(['products.addonGroups.options'])->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'slug' => $cat->slug,
                'name' => $cat->display_name,
                'image' => $cat->image,
                'products' => $cat->products->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'slug' => $p->slug,
                        'name' => $p->display_name,
                        'description' => $p->display_description,
                        'base_price' => $p->base_price ? (float) $p->base_price : null,
                        'image' => $p->image,
                        'addon_groups' => $p->addonGroups->map(function ($g) {
                            return [
                                'id' => $g->id,
                                'name' => $g->display_name,
                                'type' => $g->type ?? 'radio',
                                'is_required' => (bool) $g->is_required,
                                'min_selectable' => (int) ($g->min_selectable ?? ($g->type === 'radio' ? 1 : 0)),
                                'max_selectable' => (int) ($g->max_selectable ?: 1),
                                'options' => $g->options->map(function ($o) {
                                    return [
                                        'id' => $o->id,
                                        'name' => $o->display_name,
                                        'price' => (float) $o->price,
                                    ];
                                })->values()->all(),
                            ];
                        })->values()->all(),
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        // 2. Governorates with Areas (for Delivery Mode)
        $governoratesList = Governorate::with(['areas'])->get()->map(function ($g) use ($isAr) {
            return [
                'id' => $g->id,
                'name' => ($isAr && !empty($g->name_ar)) ? $g->name_ar : ($g->name_en ?? $g->name),
                'areas' => $g->areas->map(function ($a) use ($isAr) {
                    return [
                        'id' => $a->id,
                        'name' => ($isAr && !empty($a->name_ar)) ? $a->name_ar : ($a->name_en ?? $a->name),
                        'delivery_fee' => (float) ($a->delivery_fee ?? 0.950),
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        // 3. Store Branches (for Pickup Mode - clean list without governorate duplicates)
        $storesList = Store::all()->map(function ($s) use ($isAr) {
            return [
                'id' => $s->id,
                'name' => ($isAr && !empty($s->name_ar)) ? $s->name_ar : $s->name,
                'description' => ($isAr && !empty($s->location_description_ar))
                    ? $s->location_description_ar
                    : ($s->location_description ?? ($isAr ? 'مدينة الكويت' : 'Kuwait City')),
            ];
        })->values()->all();

        // 4. Authenticated Customer Guard Check
        $isCustomer = Auth::guard('customer')->check();
        $customerUser = $isCustomer ? Auth::guard('customer')->user() : null;

        $currentUser = $customerUser ? [
            'id' => $customerUser->id,
            'name' => $customerUser->name,
            'email' => $customerUser->email,
            'phone' => $customerUser->phone ?? '',
        ] : null;

        return view('store', compact('categories', 'governoratesList', 'storesList', 'currentUser'));
    }

    // Step: Send 6-digit OTP code[cite: 1]
    public function sendAuthCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $code = (string) random_int(100000, 999999);

        DB::table('email_verifications')->where('email', $request->email)->delete();
        DB::table('email_verifications')->insert([
            'email' => $request->email,
            'code' => $code,
            'temp_password' => Hash::make($request->password),
            'expires_at' => Carbon::now()->addMinutes(15),
            'created_at' => now(),
        ]);

        // Code logged to storage/logs/laravel.log for testing
        Log::info("Verification code for {$request->email} is: {$code}");

        return response()->json(['success' => true, 'message' => "Code sent (check logs): {$code}"]);
    }

    // Step: Verify 6-digit OTP code[cite: 1]
    public function verifyAuthCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $entry = DB::table('email_verifications')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$entry) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired code'], 422);
        }

        $user = User::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => explode('@', $request->email)[0],
                'password' => $entry->temp_password,
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user);
        DB::table('email_verifications')->where('email', $request->email)->delete();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // Step: Save Order[cite: 1]
    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'order_type' => 'required|in:delivery,pickup',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'payment_method' => 'required',
            'items' => 'required|array|min:1',
            'area_name' => 'nullable',
            'address_type' => 'nullable',
            'block' => 'nullable',
            'street' => 'nullable',
            'building' => 'nullable',
            'avenue' => 'nullable',
            'paci' => 'nullable',
            'subtotal' => 'required|numeric',
            'delivery_fee' => 'required|numeric',
            'total' => 'required|numeric',
            'special_remarks' => 'nullable',
        ]);

        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(7)),
                'order_type' => $data['order_type'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'area_name' => $data['area_name'] ?? null,
                'address_type' => $data['address_type'] ?? 'Home',
                'block' => $data['block'] ?? null,
                'street' => $data['street'] ?? null,
                'building' => $data['building'] ?? null,
                'avenue' => $data['avenue'] ?? null,
                'paci' => $data['paci'] ?? null,
                'subtotal' => $data['subtotal'],
                'delivery_fee' => $data['delivery_fee'],
                'total' => $data['total'],
                'payment_method' => $data['payment_method'],
                'special_remarks' => $data['special_remarks'] ?? null,
                'order_status' => 'pending',
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'selected_addons' => $item['addons'] ?? [],
                ]);
            }

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
            ]);
        });
    }
}
