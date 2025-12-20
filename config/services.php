<?php

return [
    'teleconsultation' => [
        'provider' => env('TELECONSULT_PROVIDER', 'jitsi'),
        'jitsi_domain' => env('JITSI_DOMAIN', 'meet.jit.si'),
        'twilio_domain' => env('TWILIO_VIDEO_DOMAIN', 'video.twilio.com'),
        'twilio_sid' => env('TWILIO_VIDEO_SID'),
        'twilio_api_key' => env('TWILIO_VIDEO_API_KEY'),
        'twilio_api_secret' => env('TWILIO_VIDEO_API_SECRET'),
    ],
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'), // log | twilio
        'twilio' => [
            'sid' => env('TWILIO_SID'),
            'token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_SMS_FROM'),
        ],
    ],

    'google' => [
        'maps_key' => env('GOOGLE_MAPS_KEY'),
    ],
];
