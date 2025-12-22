<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TABDIL') }} - {{ __('Currency Converter') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        /* AI Gradient Animation */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .ai-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #6B8DD6 50%, #8E37D7 75%, #667eea 100%);
            background-size: 300% 300%;
            animation: gradient-shift 6s ease infinite;
        }
        .ai-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ai-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(102, 126, 234, 0.35);
        }
        .ai-icon-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-white">
    <div class="bg-white">
        
        <!-- AI Announcement Banner -->
        <div class="ai-gradient">
            <div class="mx-auto max-w-7xl px-3 py-2 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex w-full flex-1 items-center justify-center sm:justify-start">
                        <span class="flex items-center gap-2 text-white text-sm font-medium">
                            <span class="pulse-dot inline-block h-2 w-2 rounded-full bg-white"></span>
                            <span class="hidden sm:inline">🤖 {{ __('NEW: AI-Powered Tools Now Available!') }}</span>
                            <span class="sm:hidden">🤖 {{ __('AI Tools Available!') }}</span>
                        </span>
                    </div>
                    <div class="order-3 mt-2 w-full flex-shrink-0 sm:order-2 sm:mt-0 sm:w-auto">
                        <a href="{{ route('register') }}" class="flex items-center justify-center rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-primary-600 shadow-sm hover:bg-gray-100 transition-all">
                            {{ __('Register Free') }} →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header -->
        <header class="absolute inset-x-0 top-14 z-50" x-data="{ mobileMenuOpen: false }">
            <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="{{ route('home') }}" class="-m-1.5 p-1.5 flex items-center gap-2">
                        <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="">
                        <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                    </a>
                </div>
                <div class="flex lg:hidden">
                    <button type="button" @click="mobileMenuOpen = true" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                        <span class="sr-only">{{ __('Open main menu') }}</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
                <div class="hidden lg:flex lg:gap-x-12">
                    <a href="{{ route('currency.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-primary-600 transition-colors">{{ __('Converter') }}</a>
                    <a href="#ai-tools" class="text-sm font-semibold leading-6 text-gray-900 hover:text-primary-600 transition-colors">{{ __('AI Tools') }}</a>
                    <a href="{{ route('login') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-primary-600 transition-colors">{{ __('Log in') }}</a>
                </div>
                <div class="hidden lg:flex lg:flex-1 lg:justify-end gap-x-4 items-center">
                    <!-- Language Switcher -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button" class="flex items-center gap-x-1 text-sm font-semibold leading-6 text-gray-900 hover:text-primary-600 transition-colors">
                            @if(app()->getLocale() == 'ar')
                                <span>🇸🇦 العربية</span>
                            @else
                                <span>🇬🇧 English</span>
                            @endif
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                            <a href="{{ route('locale.switch', 'ar') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50 text-right" role="menuitem">🇸🇦 العربية</a>
                            <a href="{{ route('locale.switch', 'en') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50 text-right" role="menuitem">🇬🇧 English</a>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('currency.index') }}" class="text-sm font-semibold leading-6 text-gray-900">{{ __('Dashboard') }} <span aria-hidden="true">&rarr;</span></a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-md ai-gradient px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all">{{ __('Register Free') }}</a>
                    @endauth
                </div>
            </nav>

            <!-- Mobile menu, show/hide based on menu open state. -->
            <div class="lg:hidden" role="dialog" aria-modal="true" x-show="mobileMenuOpen" x-cloak style="display: none;">
                <!-- Background backdrop, show/hide based on slide-over state. -->
                <div class="fixed inset-0 z-50 bg-black/20 backdrop-blur-sm" @click="mobileMenuOpen = false"></div>
                <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10" 
                     x-show="mobileMenuOpen"
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full">
                    <div class="flex items-center justify-between">
                        <a href="#" class="-m-1.5 p-1.5 flex items-center gap-2">
                            <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="">
                            <span class="text-xl font-bold text-gray-900">{{ config('app.name') }}</span>
                        </a>
                        <button type="button" @click="mobileMenuOpen = false" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                            <span class="sr-only">{{ __('Close menu') }}</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10">
                            <div class="space-y-2 py-6">
                                <a href="{{ route('currency.index') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ __('Converter') }}</a>
                                <a href="#ai-tools" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ __('AI Tools') }}</a>
                                <a href="{{ route('login') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ __('Log in') }}</a>
                            </div>
                            <div class="py-6 space-y-4">
                                <!-- Language Switcher Mobile -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900">{{ __('Language') }}</span>
                                    <div class="flex gap-4">
                                        <a href="{{ route('locale.switch', 'ar') }}" class="text-sm px-2 py-1 rounded {{ app()->getLocale() == 'ar' ? 'bg-primary-50 text-primary-600' : 'text-gray-500' }}">العربية</a>
                                        <a href="{{ route('locale.switch', 'en') }}" class="text-sm px-2 py-1 rounded {{ app()->getLocale() == 'en' ? 'bg-primary-50 text-primary-600' : 'text-gray-500' }}">English</a>
                                    </div>
                                </div>
                                @auth
                                    <a href="{{ route('currency.index') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">{{ __('Dashboard') }}</a>
                                @else
                                    <a href="{{ route('register') }}" class="flex w-full items-center justify-center rounded-md ai-gradient px-3 py-2.5 text-base font-semibold text-white shadow-sm hover:opacity-90">{{ __('Register Free') }}</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <div class="relative isolate px-6 pt-14 lg:px-8">
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#667eea] to-[#764ba2] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
            <div class="mx-auto max-w-3xl py-24 sm:py-32 lg:py-40">
                <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                    <div class="relative rounded-full px-4 py-1.5 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20 flex items-center gap-2">
                        <span class="ai-icon-bg text-white px-2 py-0.5 rounded-full text-xs font-bold">AI</span>
                        {{ __('AI-Powered Currency Conversion & Content Tools') }}
                    </div>
                </div>
                <!-- Mobile Only Badge -->
                <div class="flex justify-center mb-6 sm:hidden">
                    <div class="relative rounded-full px-3 py-1 text-xs leading-5 text-gray-600 ring-1 ring-gray-900/10 flex items-center gap-1.5 bg-white/50 backdrop-blur-sm">
                        <span class="ai-icon-bg text-white px-1.5 py-0.5 rounded-full text-[10px] font-bold">AI</span>
                        {{ __('AI-Powered Tools') }}
                    </div>
                </div>

                <div class="text-center">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-6xl">
                        {{ __('Smart Currency Conversion') }}
                        <span class="block bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent pb-2 mt-2 sm:mt-0">{{ __('Powered by AI') }}</span>
                    </h1>
                    <p class="mt-6 text-base sm:text-lg leading-7 sm:leading-8 text-gray-600 px-4 sm:px-0">{{ __('Convert currencies instantly with real-time rates. Plus, unlock AI-powered tools for content generation, translation, and more – all in one platform!') }}</p>
                    <div class="mt-8 sm:mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-x-6">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto justify-center rounded-md ai-gradient px-6 py-3 text-sm font-semibold text-white shadow-lg hover:opacity-90 transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            {{ __('Start Free with AI') }}
                        </a>
                        <a href="{{ route('currency.index') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-primary-600 transition-colors">{{ __('Try Converter') }} <span aria-hidden="true" class="rtl:rotate-180 inline-block">→</span></a>
                    </div>
                </div>
            </div>
            <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#667eea] to-[#764ba2] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
            </div>
        </div>

        <!-- AI Tools Section -->
        <div id="ai-tools" class="bg-gradient-to-b from-white to-gray-50 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full ai-gradient px-4 py-1.5 text-sm font-semibold text-white mb-6">
                        <span class="pulse-dot inline-block h-2 w-2 rounded-full bg-white"></span>
                        {{ __('Exclusive AI Tools') }}
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">{{ __('Unlock the Power of AI') }}</h2>
                    <p class="mt-4 text-lg leading-8 text-gray-600">{{ __('Register now to access our exclusive AI-powered tools – completely free!') }}</p>
                </div>
                
                <!-- AI Tools Grid -->
                <div class="mx-auto mt-16 max-w-6xl">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        
                        <!-- AI Chat -->
                        <div class="ai-card relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
                            <div class="ai-icon-bg h-12 w-12 rounded-xl flex items-center justify-center mb-6 float-animation">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('AI Chat Assistant') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('Get instant answers and assistance with our intelligent AI chatbot available 24/7.') }}</p>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Free') }}</span>
                            </div>
                        </div>

                        <!-- Content Writer -->
                        <div class="ai-card relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
                            <div class="ai-icon-bg h-12 w-12 rounded-xl flex items-center justify-center mb-6 float-animation" style="animation-delay: 0.5s;">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Content Writer') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('Generate professional blog posts, articles, and marketing content in seconds.') }}</p>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Free') }}</span>
                            </div>
                        </div>

                        <!-- SEO Keywords -->
                        <div class="ai-card relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
                            <div class="ai-icon-bg h-12 w-12 rounded-xl flex items-center justify-center mb-6 float-animation" style="animation-delay: 1s;">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('SEO Keywords') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('Extract powerful SEO keywords from your content to boost search rankings.') }}</p>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Free') }}</span>
                            </div>
                        </div>

                        <!-- Translator -->
                        <div class="ai-card relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
                            <div class="ai-icon-bg h-12 w-12 rounded-xl flex items-center justify-center mb-6 float-animation" style="animation-delay: 1.5s;">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 21l5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 016-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 01-3.827-5.802" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('AI Translator') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('Translate content between Arabic and English with natural, fluent results.') }}</p>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Free') }}</span>
                            </div>
                        </div>

                        <!-- Text Summarizer -->
                        <div class="ai-card relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
                            <div class="ai-icon-bg h-12 w-12 rounded-xl flex items-center justify-center mb-6 float-animation" style="animation-delay: 2s;">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Text Summarizer') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('Condense long articles and documents into clear, concise summaries.') }}</p>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Free') }}</span>
                            </div>
                        </div>

                        <!-- Content Rewriter -->
                        <div class="ai-card relative overflow-hidden rounded-2xl bg-white p-8 shadow-lg ring-1 ring-gray-200">
                            <div class="ai-icon-bg h-12 w-12 rounded-xl flex items-center justify-center mb-6 float-animation" style="animation-delay: 2.5s;">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ __('Content Rewriter') }}</h3>
                            <p class="text-gray-600 text-sm">{{ __('Rewrite and improve your content with different tones and styles.') }}</p>
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Free') }}</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- CTA Button -->
                <div class="mt-16 text-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-3 rounded-full ai-gradient px-8 py-4 text-lg font-semibold text-white shadow-xl hover:opacity-90 transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                        </svg>
                        {{ __('Register Now & Access All AI Tools Free') }}
                    </a>
                    <p class="mt-4 text-sm text-gray-500">{{ __('No credit card required. Start using AI instantly!') }}</p>
                </div>
            </div>
        </div>

        <!-- Feature Section - Modern Design -->
        <div id="features" class="relative bg-gradient-to-b from-gray-50 to-white py-24 sm:py-32 overflow-hidden">
            <!-- Background decorations -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-purple-200 to-indigo-200 rounded-full opacity-30 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-blue-200 to-cyan-200 rounded-full opacity-30 blur-3xl"></div>
            </div>
            
            <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary-100 to-indigo-100 px-4 py-1.5 text-sm font-semibold text-primary-700 mb-6">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                        {{ __('Why Choose Us') }}
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl lg:text-5xl">{{ __('Everything you need for currency exchange') }}</h2>
                    <p class="mt-6 text-lg leading-8 text-gray-600">{{ __('We provide the most accurate and up-to-date exchange rates for the region\'s most important currencies.') }}</p>
                </div>
                
                <div class="mx-auto mt-16 max-w-5xl">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:gap-8">
                        
                        <!-- Feature 1: Real-time Updates -->
                        <a href="{{ route('currency.index') }}" class="feature-card group relative overflow-hidden rounded-3xl bg-white p-8 shadow-xl ring-1 ring-gray-100 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 block cursor-pointer">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="relative">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 shadow-lg shadow-blue-500/30 mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">{{ __('Real-time Updates') }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ __('Our rates are updated frequently to ensure you get the most accurate conversion possible.') }}</p>
                                
                                <div class="mt-6 flex items-center text-sm font-semibold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span>{{ __('Learn more') }}</span>
                                    <svg class="w-4 h-4 ml-1 rtl:mr-1 rtl:ml-0 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Feature 2: Multiple Currencies -->
                        <a href="{{ route('currency.index') }}" class="feature-card group relative overflow-hidden rounded-3xl bg-white p-8 shadow-xl ring-1 ring-gray-100 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 block cursor-pointer">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="relative">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 shadow-lg shadow-purple-500/30 mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-purple-600 transition-colors">{{ __('Multiple Currencies') }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ __('Support for SAR, YER, OMR, USD, AED, and KWD with cross-rate calculations.') }}</p>
                                
                                <div class="mt-6 flex items-center text-sm font-semibold text-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span>{{ __('Learn more') }}</span>
                                    <svg class="w-4 h-4 ml-1 rtl:mr-1 rtl:ml-0 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Feature 3: Save Favorites -->
                        <a href="{{ route('currency.index') }}" class="feature-card group relative overflow-hidden rounded-3xl bg-white p-8 shadow-xl ring-1 ring-gray-100 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 block cursor-pointer">
                            <div class="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-orange-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-rose-100 to-orange-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="relative">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-500 to-orange-500 shadow-lg shadow-rose-500/30 mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-rose-600 transition-colors">{{ __('Save Favorites') }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ __('Save your frequently used conversions for quick access anytime.') }}</p>
                                
                                <div class="mt-6 flex items-center text-sm font-semibold text-rose-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span>{{ __('Learn more') }}</span>
                                    <svg class="w-4 h-4 ml-1 rtl:mr-1 rtl:ml-0 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Feature 4: Mobile Friendly -->
                        <a href="{{ route('currency.index') }}" class="feature-card group relative overflow-hidden rounded-3xl bg-white p-8 shadow-xl ring-1 ring-gray-100 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 block cursor-pointer">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="relative">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 shadow-lg shadow-emerald-500/30 mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-emerald-600 transition-colors">{{ __('Mobile Friendly') }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ __('Access our converter from any device, anywhere, anytime.') }}</p>
                                
                                <div class="mt-6 flex items-center text-sm font-semibold text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span>{{ __('Learn more') }}</span>
                                    <svg class="w-4 h-4 ml-1 rtl:mr-1 rtl:ml-0 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <!-- Feature 5: Direct Communication -->
                        <div onclick="document.getElementById('support-toggle-btn').click()" class="feature-card group relative overflow-hidden rounded-3xl bg-white p-8 shadow-xl ring-1 ring-gray-100 transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 block cursor-pointer col-span-1 sm:col-span-2 lg:col-span-2 max-w-lg mx-auto w-full">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-100 to-blue-100 rounded-full -translate-y-1/2 translate-x-1/2 opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="relative">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-500 shadow-lg shadow-indigo-500/30 mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <!-- Handshake / Partnership Icon -->
                                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors">{{ __('Your voice is always heard') }}</h3>
                                <p class="text-gray-600 leading-relaxed">{{ __('Your opinion matters to us. Send your suggestions, inquiries, or complaints with a single click. We ensure precise follow-up for every message.') }}</p>
                                
                                <div class="mt-6 flex items-center text-sm font-semibold text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span>{{ __('Contact Support') }}</span>
                                    <svg class="w-4 h-4 ml-1 rtl:mr-1 rtl:ml-0 transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Bottom CTA -->
                    <div class="mt-16 text-center">
                        <a href="{{ route('currency.index') }}" class="inline-flex items-center gap-2 rounded-full bg-gray-900 px-8 py-4 text-sm font-semibold text-white shadow-lg hover:bg-gray-800 transition-all hover:scale-105">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('Start Converting Now') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-900">
            <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <!-- Social links could go here -->
                </div>
                <div class="mt-8 md:order-1 md:mt-0">
                    <p class="text-center text-xs leading-5 text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved') }}.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Support Widget -->
    @include('components.support-widget')

    <!-- AI Chat Widget -->
    @include('components.ai-chat-widget')
</body>
</html>
