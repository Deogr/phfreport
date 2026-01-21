<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ticket Item Registry') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->role . '.tickets') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded shadow-sm transition-all duration-200">
                    Back to Sales
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filters -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <form method="GET" action="{{ route(auth()->user()->role . '.tickets.items') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="search" :value="__('Search Code or Guest')" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                            :value="request('search')" placeholder="e.g. ABC123XYZ or John Doe" />
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Items</option>
                            <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid (Unused)
                            </option>
                            <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used (Redeemed)
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <x-primary-button class="w-full justify-center h-[42px]">
                            {{ __('Filter Results') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Code</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Guest / Sale Info</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Redemption Info</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($items as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-primary">
                                            {{ $item->code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->is_used ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' }}">
                                                {{ $item->is_used ? 'Used' : 'Valid' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->ticket->guest_name }}</span>
                                                <span
                                                    class="text-xs text-gray-500">{{ $item->ticket->guest_phone ?? 'No phone' }}</span>
                                                <span class="text-[10px] text-gray-400 mt-1">Sale #{{ $item->ticket_id }}
                                                    ({{ $item->ticket->payment_method }})</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->is_used && $item->attendanceLogs->isNotEmpty())
                                                @php $log = $item->attendanceLogs->first(); @endphp
                                                <div class="flex flex-col">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">At
                                                        {{ $log->station->name }}</span>
                                                    <span class="text-xs text-gray-500">By {{ $log->receptionist->name }}</span>
                                                    <span
                                                        class="text-[10px] text-gray-400">{{ $log->created_at->format('M d, H:i') }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Not redeemed yet</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $item->created_at->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                            No ticket items found matching your filters.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>