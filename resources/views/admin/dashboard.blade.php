<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <a href="{{ route('currency.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('Currency Converter') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">{{ __('Total Users') }}</div>
                    <div class="text-3xl font-bold">{{ $totalUsers }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">{{ __('Exchange Rates') }}</div>
                    <div class="text-3xl font-bold">{{ $totalRates }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">{{ __('System Status') }}</div>
                    <div class="text-3xl font-bold text-green-600">{{ __('Active') }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ __('Recent Admin Logs') }}</h3>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.visitors.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700">📊 {{ __('Visitor Analytics') }}</a>
                            <a href="{{ route('admin.backup-rates') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">{{ __('Backup Rates') }}</a>
                            <a href="{{ route('admin.api-settings') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">{{ __('API Settings') }}</a>
                        </div>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Admin') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-center font-semibold">{{ $log->admin->name }}</td>
                                        <td class="px-6 py-4 text-center break-words">{{ $log->action }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600">{{ $log->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">{{ __('No logs found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden space-y-4">
                        @forelse($recentLogs as $log)
                            <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-gray-800">{{ $log->admin->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="text-sm text-gray-600 break-words">
                                    <span class="font-medium text-gray-500">{{ __('Action:') }}</span>
                                    {{ $log->action }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-4">{{ __('No logs found.') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
