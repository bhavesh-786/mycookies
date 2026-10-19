<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Events\OrderPlacedEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;

class CustomerAddressController extends Controller
{
    // ==================== DATABASE-LEVEL ADDRESS APIS ====================

    // ==================== DATABASE-LEVEL ADDRESS APIS ====================

    public function getAddresses(Request $request)
    {
        $customerId = Auth::guard('customer')->id() ?? Auth::id();
        $email = $request->query('email');
        $phone = $request->query('phone');

        // If session ID is missing, match by customer email or phone
        if (!$customerId && ($email || $phone)) {
            $customer = DB::table('customers')
                ->when($email, fn($q) => $q->where('email', $email))
                ->when(!$email && $phone, fn($q) => $q->where('phone', $phone))
                ->first();
            $customerId = $customer->id ?? null;
        }

        if (!$customerId) {
            return response()->json(['addresses' => []], 200);
        }

        $addresses = \App\Models\CustomerAddress::where('customer_id', $customerId)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return response()->json(['addresses' => $addresses], 200);
    }

    public function saveAddress(Request $request)
    {
        $customerId = Auth::guard('customer')->id() ?? Auth::id();
        $email = $request->input('email');
        $phone = $request->input('phone');

        if (!$customerId && ($email || $phone)) {
            $customer = DB::table('customers')
                ->when($email, fn($q) => $q->where('email', $email))
                ->when(!$email && $phone, fn($q) => $q->where('phone', $phone))
                ->first();

            if (!$customer && $email) {
                $customerId = DB::table('customers')->insertGetId([
                    'name'       => $request->input('name', 'Guest'),
                    'email'      => $email,
                    'phone'      => $phone ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $customerId = $customer->id ?? null;
            }
        }

        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Customer identification required.'], 422);
        }

        $validated = $request->validate([
            'id'                    => 'nullable|integer',
            'type'                  => 'required|string',
            'block'                 => 'required|string',
            'street'                => 'required|string',
            'building'              => 'required|string',
            'floor'                 => 'nullable|string',
            'flat_no'               => 'nullable|string',
            'avenue'                => 'nullable|string',
            'additional_directions' => 'nullable|string',
            'paci'                  => 'nullable|string',
            'latitude'              => 'nullable|numeric',
            'longitude'             => 'nullable|numeric',
            'is_default'            => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            \App\Models\CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        }

        $address = \App\Models\CustomerAddress::updateOrCreate(
            [
                'id'          => $request->id,
                'customer_id' => $customerId,
            ],
            [
                'type'                  => $validated['type'] ?? 'Home',
                'block'                 => $validated['block'],
                'street'                => $validated['street'],
                'building'              => $validated['building'],
                'floor'                 => $validated['floor'] ?? null,
                'flat_no'               => $validated['flat_no'] ?? null,
                'avenue'                => $validated['avenue'] ?? null,
                'additional_directions' => $validated['additional_directions'] ?? null,
                'paci'                  => $validated['paci'] ?? null,
                'latitude'              => $validated['latitude'] ?? null,
                'longitude'             => $validated['longitude'] ?? null,
                'is_default'            => $request->boolean('is_default'),
            ]
        );

        return response()->json([
            'success' => true,
            'address' => $address,
            'message' => __('Address saved successfully!')
        ]);
    }

    public function setDefaultAddress(Request $request, $id)
    {
        $customerId = Auth::guard('customer')->id() ?? Auth::id();
        $email = $request->input('email');

        if (!$customerId && $email) {
            $customer = DB::table('customers')->where('email', $email)->first();
            $customerId = $customer->id ?? null;
        }

        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        \App\Models\CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
        \App\Models\CustomerAddress::where('customer_id', $customerId)->where('id', $id)->update(['is_default' => true]);

        return response()->json(['success' => true, 'message' => __('Default address updated!')]);
    }

    public function deleteAddress(Request $request, $id)
    {
        $customerId = Auth::guard('customer')->id() ?? Auth::id();
        $email = $request->query('email');

        if (!$customerId && $email) {
            $customer = DB::table('customers')->where('email', $email)->first();
            $customerId = $customer->id ?? null;
        }

        if (!$customerId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        \App\Models\CustomerAddress::where('customer_id', $customerId)->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => __('Address deleted.')]);
    }
}
