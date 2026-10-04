<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Cairo', sans-serif;
            background-color: #F8F7F4;
            margin: 0;
            padding: 40px 20px;
            color: #24261F;
        }

        .email-card {
            max-width: 500px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 24px;
            padding: 32px;
            border: 1px solid #E7E5E4;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            text-align: center;
        }

        .logo {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #747D52, #5A623E);
            color: #FFFFFF;
            font-weight: 900;
            font-size: 16px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            line-height: 56px;
            letter-spacing: 1px;
        }

        h2 {
            font-size: 18px;
            font-weight: 800;
            color: #24261F;
            margin-bottom: 8px;
        }

        p {
            font-size: 13px;
            color: #78716C;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .btn {
            display: inline-block;
            background-color: #747D52;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 12px;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 14px;
            box-shadow: 0 4px 10px rgba(116, 125, 82, 0.25);
            transition: background 0.2s;
        }

        .btn:hover {
            background-color: #636C44;
        }

        .footer {
            font-size: 11px;
            color: #A8A29E;
            margin-top: 32px;
            border-top: 1px solid #F5F5F4;
            padding-top: 16px;
        }
    </style>
</head>

<body>
    <div class="email-card">
        <div class="logo">OW</div>
        <h2>{{ __('Verify Your Email Address') }}</h2>
        <p>
            {{ __('Hello') }} <strong>{{ $customerName }}</strong>,<br>
            {{ __('Thank you for choosing Otherwise! Please click the button below to verify your email address and securely continue your order checkout.') }}
        </p>

        <a href="{{ $verificationUrl }}" target="_blank" class="btn">{{ __('Verify Email & Continue') }}</a>

        <div class="footer">
            &copy; {{ date('Y') }} {{ __('Otherwise Specialty. All rights reserved.') }}
        </div>
    </div>
</body>

</html>
