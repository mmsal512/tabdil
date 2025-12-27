<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 {{ __('visitors.analytics_dashboard') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.visitors.settings') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm">
                    ⚙️ {{ __('visitors.settings') }}
                </a>
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← {{ __('Admin Dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Real-time Counter -->
            <div class="mb-6 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-purple-200 text-sm">{{ __('visitors.realtime_visitors') }}</div>
                        <div class="text-4xl font-bold">{{ $realtimeVisitors }}</div>
                        <div class="text-purple-200 text-xs mt-1">{{ __('visitors.last_5_minutes') }}</div>
                    </div>
                    <div class="text-6xl opacity-30">👥</div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Today's Visitors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-500 text-sm">{{ __('visitors.today_visitors') }}</div>
                            <div class="text-3xl font-bold text-blue-600">{{ $todayStats['unique_visitors'] }}</div>
                        </div>
                        <div class="text-4xl">📅</div>
                    </div>
                    @if($visitorChange['direction'] !== 'neutral')
                        <div class="mt-2 text-sm {{ $visitorChange['direction'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $visitorChange['direction'] === 'up' ? '↑' : '↓' }} {{ $visitorChange['value'] }}% {{ __('visitors.vs_yesterday') }}
                        </div>
                    @endif
                </div>

                <!-- New vs Returning -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm mb-2">{{ __('visitors.new_vs_returning') }}</div>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $todayStats['new_visitors'] }}</div>
                            <div class="text-xs text-gray-500">{{ __('visitors.new') }}</div>
                        </div>
                        <div class="text-gray-300">|</div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $todayStats['returning_visitors'] }}</div>
                            <div class="text-xs text-gray-500">{{ __('visitors.returning') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Visitors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-500 text-sm">{{ __('visitors.this_week') }}</div>
                            <div class="text-3xl font-bold text-purple-600">{{ $weeklyStats['unique_visitors'] }}</div>
                        </div>
                        <div class="text-4xl">📈</div>
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        {{ $weeklyStats['total_visits'] }} {{ __('visitors.page_views') }}
                    </div>
                </div>

                <!-- Monthly Visitors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-gray-500 text-sm">{{ __('visitors.this_month') }}</div>
                            <div class="text-3xl font-bold text-orange-600">{{ $monthlyStats['unique_visitors'] }}</div>
                        </div>
                        <div class="text-4xl">📊</div>
                    </div>
                    <div class="mt-2 text-sm text-gray-500">
                        {{ $monthlyStats['total_visits'] }} {{ __('visitors.page_views') }}
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Daily Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">{{ __('visitors.visitors_chart') }}</h3>
                    <div class="flex gap-2 mb-4">
                        <button onclick="loadChart('today')" class="chart-btn px-3 py-1 text-sm rounded bg-gray-200 hover:bg-gray-300" data-period="today">
                            {{ __('visitors.today') }}
                        </button>
                        <button onclick="loadChart('week')" class="chart-btn px-3 py-1 text-sm rounded bg-blue-500 text-white" data-period="week">
                            {{ __('visitors.week') }}
                        </button>
                        <button onclick="loadChart('month')" class="chart-btn px-3 py-1 text-sm rounded bg-gray-200 hover:bg-gray-300" data-period="month">
                            {{ __('visitors.month') }}
                        </button>
                    </div>
                    <canvas id="visitorsChart" height="200"></canvas>
                </div>

                <!-- Device Breakdown -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">{{ __('visitors.device_breakdown') }}</h3>
                    <div class="space-y-4">
                        @php
                            $deviceIcons = ['mobile' => '📱', 'desktop' => '💻', 'tablet' => '📲', 'unknown' => '❓'];
                            $total = array_sum($todayStats['device_breakdown']);
                        @endphp
                        @foreach($todayStats['device_breakdown'] as $device => $count)
                            @php $percent = $total > 0 ? round(($count / $total) * 100) : 0; @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $deviceIcons[$device] ?? '📍' }}</span>
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="capitalize">{{ $device }}</span>
                                        <span class="text-gray-500">{{ $count }} ({{ $percent }}%)</span>
                                    </div>
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full {{ $device === 'mobile' ? 'bg-green-500' : ($device === 'desktop' ? 'bg-blue-500' : 'bg-purple-500') }}" 
                                             style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Details Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Countries -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">🌍 {{ __('visitors.top_countries') }}</h3>
                    <div class="space-y-3">
                        @forelse($todayStats['country_breakdown']->take(5) as $country)
                            <div class="flex items-center justify-between">
                                <span>{{ $country->country_name ?? __('visitors.unknown') }}</span>
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $country->visits }} {{ __('visitors.single_visit') }}</span>
                            </div>
                        @empty
                            <div class="text-gray-500 text-sm">{{ __('visitors.no_data') }}</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Pages -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">📄 {{ __('visitors.top_pages') }}</h3>
                    <div class="space-y-3">
                        @forelse($todayStats['top_pages']->take(5) as $page)
                            <div class="flex items-center justify-between">
                                <span class="text-sm truncate max-w-[180px]" title="{{ $page->page_url }}">
                                    {{ str_replace(url('/'), '', $page->page_url) ?: '/' }}
                                </span>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">{{ $page->visits }} {{ __('visitors.single_visit') }}</span>
                            </div>
                        @empty
                            <div class="text-gray-500 text-sm">{{ __('visitors.no_data') }}</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Referrers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">🔗 {{ __('visitors.traffic_sources') }}</h3>
                    <div class="space-y-3">
                        @forelse($todayStats['top_referrers']->take(5) as $ref)
                            <div class="flex items-center justify-between">
                                <span class="text-sm truncate max-w-[180px]" title="{{ $ref->referrer_domain }}">
                                    {{ $ref->referrer_domain }}
                                </span>
                                <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">{{ $ref->visits }} {{ __('visitors.single_visit') }}</span>
                            </div>
                        @empty
                            <div class="text-gray-500 text-sm">{{ __('visitors.direct_traffic') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let visitorsChart = null;
        
        function loadChart(period) {
            // Update button styles
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-200');
            });
            document.querySelector(`[data-period="${period}"]`).classList.remove('bg-gray-200');
            document.querySelector(`[data-period="${period}"]`).classList.add('bg-blue-500', 'text-white');
            
            fetch(`{{ route('admin.visitors.chart-data') }}?period=${period}`)
                .then(res => res.json())
                .then(data => {
                    if (visitorsChart) {
                        visitorsChart.destroy();
                    }
                    
                    const ctx = document.getElementById('visitorsChart').getContext('2d');
                    visitorsChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: '{{ __("visitors.visitors") }}',
                                data: data.data,
                                borderColor: 'rgb(99, 102, 241)',
                                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                });
        }
        
        // Load initial chart
        document.addEventListener('DOMContentLoaded', () => loadChart('week'));
    </script>
    @endpush
</x-app-layout>
