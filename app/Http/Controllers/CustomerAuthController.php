<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('customer')->attempt($credentials, true)) {
            $request->session()->regenerate();
            $customer = Auth::guard('customer')->user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Invalid email or password.')
        ], 422);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('customer')->login($customer, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone ?? '',
            ],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        return response()->json(['success' => true]);
    }

    public function deleteAccount(Request $request)
    {
        /** @var \App\Models\Customer|null $customer */
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            Auth::guard('customer')->logout();
            $customer->delete();
        }

        return response()->json(['success' => true]);
    }
}
