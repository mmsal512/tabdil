<x-app-layout>
    <x-slot name="header">
        {{ __('AI Request Logs') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Recent Activity') }}
                        </h2>
                    </div>

                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-start text-gray-500 min-w-[800px]">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3 px-6 whitespace-nowrap text-start">
                                        {{ __('User') }}
                                    </th>
                                    <th scope="col" class="py-3 px-6 whitespace-nowrap text-start">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="py-3 px-6 whitespace-nowrap text-start">
                                        {{ __('Tokens') }}
                                    </th>
                                    <th scope="col" class="py-3 px-6 w-1/4 min-w-[200px] text-start">
                                        {{ __('Input') }}
                                    </th>
                                    <th scope="col" class="py-3 px-6 w-1/4 min-w-[200px] text-start">
                                        {{ __('Output') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="bg-white border-b hover:bg-gray-50 align-top">
                                        <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap text-start">
                                            {{ $log->user ? $log->user->name : __('Visitor/System') }}
                                            <div class="text-xs text-gray-400">
                                                {{ $log->user ? $log->user->email : '' }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-start">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $log->created_at->diffForHumans() }}</span>
                                                <span class="text-xs text-gray-400" dir="ltr">{{ $log->created_at->format('Y-m-d H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-start">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                                {{ $log->tokens }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-start">
                                            <div class="max-h-20 overflow-y-auto" title="{{ $log->input_text }}">
                                                {{ Str::limit($log->input_text, 100) }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-start">
                                            <div class="max-h-20 overflow-y-auto" title="{{ $log->output_text }}">
                                                {{ Str::limit($log->output_text, 100) }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="5" class="py-4 px-6 text-center text-gray-500">
                                            {{ __('No logs found currently.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4" dir="ltr">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
