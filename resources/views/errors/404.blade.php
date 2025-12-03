<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <div class="mb-8">
                <h1 class="text-9xl font-bold text-indigo-600">404</h1>
                <div class="text-4xl font-semibold text-gray-800 mt-4">Page Not Found</div>
                <p class="text-gray-600 mt-4 text-lg">
                    Oops! The page you're looking for doesn't exist.
                </p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('currency.index') }}" 
                   class="block w-full px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    Go to Currency Converter
                </a>
                
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="block w-full px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Login
                    </a>
                @endauth
            </div>

            <div class="mt-8">
                <p class="text-sm text-gray-500">
                    If you believe this is an error, please contact support.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
