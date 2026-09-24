<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'order_type'     => 'required|string',
            'customer_name'  => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'subtotal'       => 'required|numeric',
            'delivery_fee'   => 'nullable|numeric',
            'total'          => 'required|numeric',
            'payment_method' => 'required|string',
            'items'          => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();

            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $customerId = Auth::guard('customer')->id() ?? null;

            $order = Order::create([
                'order_number'    => $orderNumber,
                'user_id'         => $customerId,
                'order_type'      => $request->order_type,
                'store_id'        => $request->store_id ?? null,
                'customer_name'   => $request->customer_name,
                'customer_email'  => $request->customer_email,
                'customer_phone'  => $request->customer_phone,
                'area_name'       => $request->area_name,
                'address_type'    => $request->address_type ?? 'Home',
                'block'           => $request->block,
                'street'          => $request->street,
                'building'        => $request->building,
                'avenue'          => $request->avenue ?? null,
                'paci'            => $request->paci ?? null,
                'payment_method'  => $request->payment_method,
                'subtotal'        => $request->subtotal,
                'delivery_fee'    => $request->delivery_fee ?? 0.000,
                'total'           => $request->total,
                'status'          => 'pending',
                'special_remarks' => $request->special_remarks ?? null,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item['product_id'] ?? null,
                    'name'            => $item['name'] ?? '',
                    'quantity'        => $item['quantity'],
                    'unit_price'      => $item['unit_price'],
                    'total_price'     => $item['total_price'],
                    'selected_addons' => !empty($item['addons']) ? json_encode($item['addons']) : null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'order_number' => $orderNumber,
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
