<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TABDIL') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-gray-900">
        <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50">
            <div class="sm:mx-auto sm:w-full sm:max-w-md">
                <a href="/" class="flex justify-center">
                    <img class="h-12 w-auto" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
                </a>
                <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                    {{ $header ?? config('app.name') }}
                </h2>
                
                <!-- Language Switcher -->
                <div class="mt-4 flex justify-center gap-2">
                    <a href="{{ route('locale.switch', 'ar') }}" class="px-3 py-1 text-sm rounded-md {{ app()->getLocale() == 'ar' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} transition-colors">
                        العربية
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}" class="px-3 py-1 text-sm rounded-md {{ app()->getLocale() == 'en' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }} transition-colors">
                        English
                    </a>
                </div>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
                <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12 border border-gray-100">
                    {{ $slot }}
                </div>

                <p class="mt-10 text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved') }}.
                </p>
            </div>
        </div>
    </body>
</html>
