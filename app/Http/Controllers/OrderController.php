<?php

namespace App\Http\Controllers;

use App\Events\OrderPlacedEvent;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'order_type'            => 'required|string',
            'store_id'              => 'nullable|integer',
            'area_id'               => 'nullable|integer',
            'customer_name'         => 'required|string',
            'customer_email'        => 'required|email',
            'customer_phone'        => 'required|string',
            'subtotal'              => 'required|numeric',
            'delivery_fee'          => 'nullable|numeric',
            'total'                 => 'required|numeric',
            'payment_method'        => 'required|string',
            'items'                 => 'required|array|min:1',
            'address_type'          => 'nullable|string',
            'block'                 => 'nullable|string',
            'street'                => 'nullable|string',
            'building'              => 'nullable|string',
            'floor'                 => 'nullable|string',
            'flat_no'               => 'nullable|string',
            'avenue'                => 'nullable|string',
            'additional_directions' => 'nullable|string',
            'paci'                  => 'nullable|string',
            'latitude'              => 'nullable|numeric',
            'longitude'             => 'nullable|numeric',
            'special_remarks'       => 'nullable|string',
            'save_address_to_db'    => 'nullable|boolean',
            'set_default_address'   => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $customerId = Auth::guard('customer')->id() ?? null;
            $initialStatus = ($request->payment_method === 'cash') ? 'pending' : 'awaiting_payment';

            // If user is authenticated and checked "save address", persist to customer_addresses table
            if ($customerId && $request->boolean('save_address_to_db') && $request->order_type === 'delivery') {
                if ($request->boolean('set_default_address')) {
                    CustomerAddress::where('customer_id', $customerId)->update(['is_default' => false]);
                }

                CustomerAddress::updateOrCreate(
                    [
                        'customer_id' => $customerId,
                        'type'        => $request->address_type ?? 'Home',
                        'block'       => $request->block,
                        'street'      => $request->street,
                        'building'    => $request->building,
                    ],
                    [
                        'area_id'               => $request->area_id,
                        'area_name'             => $request->area_name,
                        'avenue'                => $request->avenue,
                        'floor'                 => $request->floor,
                        'flat_no'               => $request->flat_no,
                        'additional_directions' => $request->additional_directions,
                        'paci'                  => $request->paci,
                        'latitude'              => $request->latitude,
                        'longitude'             => $request->longitude,
                        'is_default'            => $request->boolean('set_default_address'),
                    ]
                );
            }

            $order = Order::create([
                'order_number'          => $orderNumber,
                'user_id'               => $customerId,
                'order_type'            => $request->order_type,
                'store_id'              => $request->store_id ?? null,
                'area_id'               => $request->area_id ?? null,
                'customer_name'         => $request->customer_name,
                'customer_email'        => $request->customer_email,
                'customer_phone'        => $request->customer_phone,
                'area_name'             => $request->area_name,
                'address_type'          => $request->address_type ?? 'Home',
                'block'                 => $request->block,
                'street'                => $request->street,
                'building'              => $request->building,
                'floor'                 => $request->floor ?? null,
                'flat_no'               => $request->flat_no ?? null,
                'avenue'                => $request->avenue ?? null,
                'additional_directions' => $request->additional_directions ?? null,
                'paci'                  => $request->paci ?? null,
                'latitude'              => $request->latitude ?? null,
                'longitude'             => $request->longitude ?? null,
                'payment_method'        => $request->payment_method,
                'subtotal'              => $request->subtotal,
                'delivery_fee'          => $request->delivery_fee ?? 0.000,
                'total'                 => $request->total,
                'status'                => $initialStatus,
                'special_remarks'       => $request->special_remarks ?? null,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item['product_id'] ?? null,
                    'product_name'    => $item['name'] ?? '', // matching your DB column 'product_name'
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $item['unit_price'],
                    'total_price'     => $item['total_price'],
                    'selected_addons' => !empty($item['addons']) ? json_encode($item['addons']) : null,
                    'notes'           => $item['notes'] ?? null,
                ]);
            }

            DB::commit();

            if ($request->payment_method !== 'cash') {
                $mfPayment = new MyFatoorahPayment([
                    'apiKey'      => config('myfatoorah.api_key'),
                    'isTest'      => config('myfatoorah.is_test'),
                    'countryCode' => config('myfatoorah.vc_code'),
                ]);

                $postFields = [
                    'NotificationOption' => 'LNK',
                    'InvoiceValue'       => $order->total,
                    'CustomerName'       => $order->customer_name,
                    'CustomerEmail'      => $order->customer_email,
                    'CustomerMobile'     => $order->customer_phone,
                    'DisplayCurrencyIso' => 'KWD',
                    'CustomerReference'  => $order->order_number,
                    'CallBackUrl'        => route('myfatoorah.callback'),
                    'ErrorUrl'           => route('myfatoorah.error'),
                    'Language'           => app()->getLocale() === 'ar' ? 'ar' : 'en',
                ];

                $mfData = $mfPayment->getInvoiceURL($postFields, 0);

                if (isset($mfData['invoiceId'])) {
                    $order->update(['payment_id' => $mfData['invoiceId']]);
                }

                return response()->json([
                    'success'      => true,
                    'redirect_url' => $mfData['invoiceURL'],
                    'order_number' => $orderNumber,
                ]);
            }

            event(new OrderPlacedEvent($order));

            return response()->json([
                'success'      => true,
                'order_number' => $orderNumber,
                'message'      => __('Order placed successfully!'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function customerOrders(Request $request)
    {
        $customerUser = Auth::guard('customer')->user();
        $customerId = $customerUser ? $customerUser->id : null;
        $email = trim($request->query('email') ?? ($customerUser ? $customerUser->email : ''));
        $phone = trim($request->query('phone') ?? ($customerUser ? $customerUser->phone : ''));

        // If completely unidentifiable, return empty
        if (!$customerId && empty($email) && empty($phone)) {
            return response()->json(['orders' => []]);
        }

        $query = Order::with('items')->latest();

        // Match either by customer ID, or fallback to their email/phone
        $query->where(function ($q) use ($customerId, $email, $phone) {
            if ($customerId) {
                $q->orWhere('user_id', $customerId);
            }
            if (!empty($email)) {
                $q->orWhereRaw('LOWER(customer_email) = ?', [strtolower($email)]);
            }
            if (!empty($phone)) {
                $q->orWhere('customer_phone', $phone);
            }
        });

        $orders = $query->get()->map(function ($order) {
            $createdTimestamp = $order->created_at ? $order->created_at->timestamp : now()->timestamp;
            $secondsPassed = now()->timestamp - $createdTimestamp;
            $remainingSeconds = max(0, 300 - $secondsPassed); // 5 minutes window

            return [
                'id'                       => $order->id,
                'order_number'             => $order->order_number,
                'order_type'               => $order->order_type,
                'area_name'                => $order->area_name,
                'status'                   => $order->status,
                'subtotal'                 => (float) $order->subtotal,
                'delivery_fee'             => (float) $order->delivery_fee,
                'total'                    => (float) $order->total,
                'payment_method'           => $order->payment_method,
                'created_at'               => $order->created_at ? $order->created_at->format('d M Y, h:i A') : '',
                'can_cancel'               => $remainingSeconds > 0 && in_array(strtolower($order->status), ['pending', 'placed']),
                'remaining_cancel_seconds' => $remainingSeconds,
                'items'                    => $order->items->map(function ($item) {
                    $rawAddons = $item->selected_addons ?? $item->addons ?? [];
                    $parsedAddons = is_string($rawAddons) ? json_decode($rawAddons, true) : $rawAddons;

                    return [
                        'id'          => $item->id,
                        'name'        => $item->name ?? $item->product_name ?? 'Cookie Item',
                        'quantity'    => $item->quantity,
                        'unit_price'  => (float) $item->unit_price,
                        'total_price' => (float) $item->total_price,
                        'addons'      => is_array($parsedAddons) ? $parsedAddons : [],
                    ];
                })->values(),
            ];
        });

        return response()->json(['orders' => $orders]);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if (Auth::guard('customer')->check() && $order->user_id && $order->user_id != Auth::guard('customer')->id()) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $secondsPassed = now()->timestamp - $order->created_at->timestamp;
        if ($secondsPassed > 300) {
            return response()->json([
                'success' => false,
                'message' => __('Cancellation period has expired (5 minutes max).')
            ], 422);
        }

        if (in_array(strtolower($order->status), ['cancelled', 'delivered', 'shipped'])) {
            return response()->json([
                'success' => false,
                'message' => __('Order cannot be cancelled at this stage.')
            ], 422);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => __('Order cancelled successfully.')
        ]);
    }
}
