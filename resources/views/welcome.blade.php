<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TABDIL - {{ __('Currency Converter') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col items-center justify-center p-6">
        
        <div class="w-full max-w-4xl flex flex-col items-center justify-center text-center space-y-8">
            
            <!-- Logo -->
            <div class="mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="TABDIL Logo" class="h-32 w-auto mx-auto">
            </div>

            <!-- Title -->
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight text-gray-900 dark:text-white">
                TABDIL
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                {{ __('Smart Currency Converter') }}
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            {{ __('Go to Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            {{ __('Log in') }}
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-8 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-semibold transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                {{ __('Register') }}
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Footer -->
            <div class="mt-16 text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} TABDIL. {{ __('All rights reserved') }}.
            </div>
        </div>

    </body>
</html>
