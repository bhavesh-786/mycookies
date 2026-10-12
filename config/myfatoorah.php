<?php

return [
    /**
     * API Token Key (string)
     * Accepted value:
     * Live Token: https://myfatoorah.readme.io/docs/live-token
     * Test Token: https://myfatoorah.readme.io/docs/test-token
     */
    'api_key' => env('MYFATOORAH_API_KEY', ''),

    /**
     * Test Mode (boolean)
     * Accepted value: true for the test mode or false for the live mode
     */
    'is_test' => env('MYFATOORAH_TEST_MODE', true),

    /**
     * Vendor Country ISO Code (string)
     * Accepted value: KWT, SAU, ARE, QAT, BHR, OMN, JOD, or EGY
     */
    'vc_code' => env('MYFATOORAH_COUNTRY_CODE', 'KWT'),

    /**
     * Save card (boolean)
     * Accepted value: true if you want to enable save card options
     */
    'save_card' => env('MYFATOORAH_SAVE_CARD', false),

    /**
     * Webhook secret key (string)
     * Enables webhook on your MyFatoorah account setting then paste the secret key here
     */
    'webhook_secret_key' => env('MYFATOORAH_WEBHOOK_SECRET', ''),

    /**
     * Register Apple Pay (boolean)
     * Accepted value: true to show the Apple Pay button on the checkout page
     */
    'register_apple_pay' => env('MYFATOORAH_REGISTER_APPLE_PAY', false),

    /**
     * Supplier code (integer)
     * Accepted value: number or null
     */
    'supplier_code' => env('MYFATOORAH_SUPPLIER_CODE', null),
];