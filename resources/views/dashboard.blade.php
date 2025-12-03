<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
        <a href="{{ route('currency.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← {{ __('Currency Converter') }}
            </a>
            </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Your Saved Conversions') }}</h3>
                        <button 
                            id="delete-selected-btn"
                            onclick="submitBulkDelete()"
                            class="hidden bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-xl transition-colors flex items-center shadow-md transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Delete Selected') }}
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($favorites->isEmpty())
                        <p class="text-gray-500">{{ __("You haven't saved any favorites yet.") }}</p>
                        <a href="{{ route('currency.index') }}" class="text-indigo-600 hover:text-indigo-900 mt-2 inline-block">{{ __('Go to Converter') }}</a>
                    @else
                        <div class="overflow-hidden rounded-xl shadow-lg border-2 border-gray-100 overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                                    <tr>
                                        <th class="px-4 py-4 text-center">
                                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-5 h-5">
                                        </th>
                                        <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Label') }}</th>
                                        <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('From') }}</th>
                                        <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('To') }}</th>
                                        <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Amount') }}</th>
                                        <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Converted Amount') }}</th>
                                        <th class="px-8 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($favorites as $favorite)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-4 text-center">
                                                <input type="checkbox" value="{{ $favorite->id }}" class="favorite-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-5 h-5">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center font-medium text-gray-800">{{ $favorite->label ?: __('My Conversion') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ __($favorite->base_currency) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ __($favorite->target_currency) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ number_format($favorite->amount) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-indigo-600">
                                                @if($favorite->converted_amount)
                                                    {{ number_format($favorite->converted_amount) }} {{ __($favorite->target_currency) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <form id="delete-form-{{ $favorite->id }}" action="{{ route('favorites.destroy', $favorite) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button 
                                                    type="button" 
                                                    onclick="showDeleteModal({{ $favorite->id }}, '{{ $favorite->label ?: __('My Conversion') }}', '{{ __($favorite->base_currency) }}', '{{ __($favorite->target_currency) }}', '{{ number_format($favorite->amount) }}', '{{ $favorite->converted_amount ? number_format($favorite->converted_amount) : '-' }}')" 
                                                    class="text-red-600 hover:text-red-900 font-semibold p-2 hover:bg-red-50 rounded-lg transition-colors">
                                                    {{ __('Delete') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('favorites.destroyMany') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- Individual Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full transform transition-all scale-95 modal-content">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-t-3xl p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        {{ __('Confirm Delete') }}
                    </h3>
                    <button onclick="closeDeleteModal()" class="text-white/80 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <p class="text-gray-700 text-lg text-center font-medium">
                    {{ __('Are you sure you want to delete this favorite?') }}
                </p>

                <!-- Favorite Details Card -->
                <div class="bg-gradient-to-br from-gray-50 to-red-50 rounded-2xl p-5 border-2 border-red-100">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ __('Label') }}:</span>
                            <span class="text-gray-900 font-bold" id="modal-delete-label">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ __('From') }}:</span>
                            <span class="text-gray-900 font-bold" id="modal-delete-from">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ __('To') }}:</span>
                            <span class="text-gray-900 font-bold" id="modal-delete-to">-</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ __('Amount') }}:</span>
                            <span class="text-gray-900 font-bold" id="modal-delete-amount">-</span>
                        </div>
                        <div class="h-px bg-red-200 my-2"></div>
                        <div class="flex justify-between items-center bg-white rounded-xl p-3">
                            <span class="text-red-600 font-semibold">{{ __('Converted Amount') }}:</span>
                            <span class="text-red-600 font-bold text-xl" id="modal-delete-converted">-</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 pb-6 flex gap-3">
                <button 
                    onclick="closeDeleteModal()" 
                    class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all transform hover:scale-105">
                    {{ __('Cancel') }}
                </button>
                <button 
                    onclick="submitDelete()" 
                    class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold hover:from-red-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg">
                    {{ __('Delete') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Confirmation Modal -->
    <div id="bulk-delete-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full transform transition-all scale-95 modal-content">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 rounded-t-3xl p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <svg class="w-7 h-7 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{ __('Confirm Delete') }}
                    </h3>
                    <button onclick="closeBulkDeleteModal()" class="text-white/80 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 space-y-6">
                <p class="text-gray-700 text-lg text-center font-medium">
                    {{ __('Are you sure you want to delete the selected favorites?') }}
                </p>
                <p class="text-gray-500 text-center text-sm">
                    <span id="selected-count" class="font-bold text-gray-900">0</span> {{ __('selected') }}
                </p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button onclick="closeBulkDeleteModal()" class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all transform hover:scale-105">
                    {{ __('Cancel') }}
                </button>
                <button onclick="confirmBulkDelete()" class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold hover:from-red-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-lg">
                    {{ __('Delete Selected') }}
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentDeleteFormId = null;

        function showDeleteModal(favoriteId, label, from, to, amount, converted) {
            currentDeleteFormId = favoriteId;
            
            // Populate modal with favorite details
            document.getElementById('modal-delete-label').textContent = label || '-';
            document.getElementById('modal-delete-from').textContent = from;
            document.getElementById('modal-delete-to').textContent = to;
            document.getElementById('modal-delete-amount').textContent = amount;
            document.getElementById('modal-delete-converted').textContent = converted;
            
            // Show modal
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.modal-content').classList.remove('scale-95');
                modal.querySelector('.modal-content').classList.add('scale-100');
            }, 10);
        }
        
        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.querySelector('.modal-content').classList.remove('scale-100');
            modal.querySelector('.modal-content').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }
        
        function submitDelete() {
            if (currentDeleteFormId) {
                document.getElementById('delete-form-' + currentDeleteFormId).submit();
            }
        }

        // Bulk Delete Scripts
        const selectAllCheckbox = document.getElementById('select-all');
        const favoriteCheckboxes = document.querySelectorAll('.favorite-checkbox');
        const deleteSelectedBtn = document.getElementById('delete-selected-btn');
        const selectedCountSpan = document.getElementById('selected-count');

        function updateDeleteButton() {
            const checkedCount = document.querySelectorAll('.favorite-checkbox:checked').length;
            if (checkedCount > 0) {
                deleteSelectedBtn.classList.remove('hidden');
                if (selectedCountSpan) selectedCountSpan.textContent = checkedCount;
            } else {
                deleteSelectedBtn.classList.add('hidden');
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                favoriteCheckboxes.forEach(cb => cb.checked = this.checked);
                updateDeleteButton();
            });
        }

        favoriteCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateDeleteButton();
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else if (document.querySelectorAll('.favorite-checkbox:checked').length === favoriteCheckboxes.length) {
                    selectAllCheckbox.checked = true;
                }
            });
        });

        function submitBulkDelete() {
            const checkedCount = document.querySelectorAll('.favorite-checkbox:checked').length;
            if (checkedCount === 0) return;

            if (selectedCountSpan) selectedCountSpan.textContent = checkedCount;

            const modal = document.getElementById('bulk-delete-modal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.modal-content').classList.remove('scale-95');
                modal.querySelector('.modal-content').classList.add('scale-100');
            }, 10);
        }

        function closeBulkDeleteModal() {
            const modal = document.getElementById('bulk-delete-modal');
            modal.querySelector('.modal-content').classList.remove('scale-100');
            modal.querySelector('.modal-content').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        function confirmBulkDelete() {
            const form = document.getElementById('bulk-delete-form');
            // Clear previous inputs
            const existingInputs = form.querySelectorAll('input[name="ids[]"]');
            existingInputs.forEach(input => input.remove());

            // Add selected IDs
            document.querySelectorAll('.favorite-checkbox:checked').forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                form.appendChild(input);
            });

            form.submit();
        }
    </script>
    @endpush
</x-app-layout>
