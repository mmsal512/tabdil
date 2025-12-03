<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full glass-card rounded-2xl p-8 space-y-8 gradient-border hover-lift">
            <!-- Logo/Header -->
            <div class="text-center">
                <h2 class="text-4xl font-extrabold gradient-text">{{ __('Welcome Back') }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ __('Sign in to access your account') }}</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                           class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-2">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 px-4 py-3">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" type="checkbox" 
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" 
                               name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" 
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-gradient-primary text-white font-bold py-3 px-4 rounded-xl hover-lift transition-all duration-200 shadow-lg hover:shadow-xl">
                    {{ __('Sign In') }}
                </button>

                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                            {{ __('Register here') }}
                        </a>
                    </p>
                </div>
            </form>

            <!-- Continue as Guest -->
            <div class="pt-6 border-t border-gray-200">
                <a href="{{ route('currency.index') }}" 
                   class="flex items-center justify-center w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    {{ __('Continue as Guest') }}
                </a>
                <p class="mt-2 text-center text-xs text-gray-500">
                    {{ __('Browse currency rates without an account') }}
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
