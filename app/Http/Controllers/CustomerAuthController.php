<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

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
            'email'    => 'required|email|max:255|unique:customers,email',
            'phone'    => 'required|string|max:20|unique:customers,phone',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => __('This email address is already registered.'),
            'phone.unique' => __('This phone number is already registered.'),
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

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ], [
            'email.exists' => __('We could not find an account with that email address.'),
        ]);

        // Send password reset link using Laravel's password broker
        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
        ], 422);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($customer));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => __($status),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __($status),
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

    public function sendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
        ]);

        // Find or create an unverified customer record for the guest
        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make(Str::random(16)),
                'is_verified' => false, // or email_verified_at => null depending on your schema
            ]
        );

        // Generate a secure temporary signed verification URL valid for 60 minutes
        $verificationUrl = URL::temporarySignedRoute(
            'customer.verify.email',
            now()->addMinutes(60),
            ['id' => $customer->id]
        );

        // Send the verification email
        try {
            Mail::raw("Hello {$customer->name},\n\nPlease click the link below to verify your email address and continue your order:\n\n{$verificationUrl}", function ($message) use ($customer) {
                $message->to($customer->email)
                    ->subject(__('Verify Your Email Address - Otherwise'));
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to send verification email. Please try again.')
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => __('Verification email sent successfully! Please check your inbox.')
        ]);
    }

    public function verifyEmail(Request $request, $id)
    {
        // Validate signed URL signature
        if (!$request->hasValidSignature()) {
            return redirect('/profile/email-signin?verified=0')->with('error', __('The verification link is invalid or has expired.'));
        }

        $customer = Customer::findOrFail($id);

        // Mark as verified
        $customer->forceFill([
            'is_verified' => true,
            'email_verified_at' => now(),
        ])->save();

        // Redirect back to frontend with verified flag (which triggers your initRouter success message)
        return redirect('/?verified=1');
    }
}
