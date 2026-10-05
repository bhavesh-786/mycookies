<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\AddonGroup;
use App\Models\AddonOption;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{

    // 0. Authentication Methods
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }


    // ==========================================
    // PASSWORD RESET WORKFLOW
    // ==========================================

    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token)
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // 1. Dashboard Overview
    public function dashboard()
    {
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $categoriesCount = Category::count();
        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.dashboard', compact('ordersCount', 'productsCount', 'categoriesCount', 'recentOrders'));
    }

    // 2. Orders Management
    public function orders()
    {
        $orders = Order::with('items')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    use Illuminate\Http\Request;
    use App\Models\Order;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;

    public function updateOrderStatus(Request $request, Order $order)
    {
        // Accept either 'order_status' or 'status'
        $request->validate([
            'order_status' => 'nullable|string',
            'status'       => 'nullable|string',
        ]);

        $newStatus = trim($request->input('order_status') ?? $request->input('status') ?? 'preparing');

        // Build update payload depending on which columns exist in the table
        $updateData = [];
        if (Schema::hasColumn('orders', 'order_status')) {
            $updateData['order_status'] = $newStatus;
        }
        if (Schema::hasColumn('orders', 'status')) {
            $updateData['status'] = $newStatus;
        }
        if (Schema::hasColumn('orders', 'updated_at')) {
            $updateData['updated_at'] = now();
        }

        if (!empty($updateData)) {
            DB::table('orders')->where('id', $order->id)->update($updateData);
        }

        // Return JSON if triggered by fetch/AJAX from the dashboard
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'order_id'     => $order->id,
                'order_status' => $newStatus,
                'message'      => __('Order status updated successfully.'),
            ]);
        }

        // Standard redirect back for classic Blade form submissions
        return back()->with('success', __('Order status updated successfully.'));
    }
}
