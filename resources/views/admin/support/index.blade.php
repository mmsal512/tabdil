<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Support Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filters & Stats -->
            <div class="mb-6 flex flex-col md:flex-row justify-between gap-4">
                <!-- Status Filter -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.support.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status', 'all') == 'all' ? 'bg-primary-600 text-white' : 'bg-white text-gray-700' }}">
                        {{ __('All') }}
                    </a>
                    <a href="{{ route('admin.support.index', ['status' => 'new']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'new' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-700' }}">
                        {{ __('New') }}
                    </a>
                    <a href="{{ route('admin.support.index', ['status' => 'in_progress']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'in_progress' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700' }}">
                        {{ __('In Progress') }}
                    </a>
                    <a href="{{ route('admin.support.index', ['status' => 'resolved']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') == 'resolved' ? 'bg-green-500 text-white' : 'bg-white text-gray-700' }}">
                        {{ __('Resolved') }}
                    </a>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('admin.support.index') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search messages...') }}" class="pl-10 pr-4 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Sender') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Type') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">
                                    {{ __('Message') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 font-bold">
                                                {{ substr($ticket->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="ltr:ml-4 rtl:mr-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $ticket->name ?: __('Anonymous') }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $ticket->email ?: '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $ticket->type == 'Complaint' ? 'bg-red-100 text-red-800' : 
                                               ($ticket->type == 'Suggestion' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ __($ticket->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $ticket->message }}">
                                            {{ Str::limit($ticket->message, 80) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $ticket->status == 'new' ? 'bg-yellow-100 text-yellow-800' : 
                                               ($ticket->status == 'resolved' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ __($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ticket->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <!-- Reply Button -->
                                            @if($ticket->email)
                                                <a href="mailto:{{ $ticket->email }}?subject={{ __('Re: Your message to Tabdil') }}&body={{ __('Hello') }} {{ $ticket->name }},%0D%0A%0D%0A{{ __('Regarding your message:') }}%0D%0A{{ $ticket->message }}%0D%0A%0D%0A..." 
                                                   class="text-blue-600 hover:text-blue-900 transition-colors" title="{{ __('Reply via Email') }}">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            <!-- Status Dropdown (Alpine) -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" class="text-gray-600 hover:text-gray-900 transition-colors" title="{{ __('Change Status') }}">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="absolute z-10 w-32 mt-2 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none right-0 transform opacity-100 scale-100">
                                                    <div class="py-1">
                                                        <form method="POST" action="{{ route('admin.support.updateStatus', $ticket) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button name="status" value="new" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">{{ __('New') }}</button>
                                                            <button name="status" value="in_progress" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">{{ __('In Progress') }}</button>
                                                            <button name="status" value="resolved" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">{{ __('Resolved') }}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete Button -->
                                            <form method="POST" action="{{ route('admin.support.destroy', $ticket) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this ticket?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="{{ __('Delete') }}">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                        {{ __('No tickets found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $tickets->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
