<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     if (Auth::guard('customer')->attempt($credentials, true)) {
    //         $request->session()->regenerate();
    //         $customer = Auth::guard('customer')->user();

    //         return response()->json([
    //             'success' => true,
    //             'user' => [
    //                 'id' => $customer->id,
    //                 'name' => $customer->name,
    //                 'email' => $customer->email,
    //                 'phone' => $customer->phone ?? '',
    //             ],
    //         ]);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => __('Invalid email or password.')
    //     ], 422);
    // }

    // public function register(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:customers,email',
    //         'phone' => 'nullable|string|max:20',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     $customer = Customer::create([
    //         'name' => $validated['name'],
    //         'email' => $validated['email'],
    //         'phone' => $validated['phone'] ?? null,
    //         'password' => Hash::make($validated['password']),
    //     ]);

    //     Auth::guard('customer')->login($customer, true);
    //     $request->session()->regenerate();

    //     return response()->json([
    //         'success' => true,
    //         'user' => [
    //             'id' => $customer->id,
    //             'name' => $customer->name,
    //             'email' => $customer->email,
    //             'phone' => $customer->phone ?? '',
    //         ],
    //     ]);
    // }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:customers,email', // change to 'users,email' if using users table
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $customer = Customer::create([
            'name'     => $validated['name'],
            'email'    => strtolower($validated['email']),
            'phone'    => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        // Send Laravel's standard verification email
        event(new Registered($customer));

        // DO NOT log the customer in here
        return response()->json([
            'success'          => true,
            'requires_verify'  => true,
            'message'          => __('Registration successful! A verification link has been sent to your email. Please verify your email before logging in.'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email'    => strtolower($request->email),
            'password' => $request->password,
        ];

        if (Auth::guard('customer')->attempt($credentials)) {
            /** @var \App\Models\Customer|\App\Models\User $user */
            $user = Auth::guard('customer')->user();

            // Block unverified users
            if (!$user->hasVerifiedEmail()) {
                Auth::guard('customer')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'message' => __('Your email address is not verified. Please check your inbox and confirm your email before logging in.'),
                ], 403);
            }

            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'user'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Invalid email or password.'),
        ], 422);
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
