<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Optional: eager load order aggregates if relationships exist
        // withCount('orders')->withSum('orders', 'total_amount')

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');

        if (in_array($sortBy, ['name', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $customers = $query->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        // Fetch the customer's orders
        $orders = \App\Models\Order::where('user_id', $customer->id)
            ->latest()
            ->paginate(10);

        // Calculate actual aggregates
        $totalOrders = Order::where('user_id', $customer->id)->count();
        $totalSpent  = Order::where('user_id', $customer->id)->sum('total'); // adjust 'total_amount' to your orders table column (e.g., 'total', 'grand_total')

        return view('admin.customers.show', compact('customer', 'orders', 'totalOrders', 'totalSpent'));
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', __('Customer deleted successfully.'));
    }
}
