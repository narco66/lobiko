<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: radial-gradient(circle at 10% 20%, rgba(102, 126, 234, 0.08), transparent 35%),
                            radial-gradient(circle at 90% 10%, rgba(118, 75, 162, 0.08), transparent 30%),
                            #f6f7fb;
            }
            .auth-shell {
                max-width: 420px;
            }
            .auth-card {
                border: 1px solid #e5e7f3;
                box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
                border-radius: 18px;
            }
            .auth-title {
                font-weight: 800;
                font-size: 1.5rem;
                margin-bottom: 0.25rem;
                color: #111827;
            }
            .auth-subtitle {
                color: #6b7280;
                font-size: 0.95rem;
            }
            .logo-gradient {
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen d-flex flex-column justify-content-center align-items-center py-5">
            <div class="text-center mb-3">
                <a href="/" class="text-decoration-none">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                    <div class="auth-title logo-gradient">LOBIKO</div>
                </a>
                <div class="auth-subtitle">Rejoignez la plateforme santé unifiée</div>
            </div>

            <div class="w-100 auth-shell px-3">
                <div class="bg-white auth-card px-4 py-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
