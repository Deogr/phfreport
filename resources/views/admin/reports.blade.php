<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Admin</a>
            <span class="mx-2 text-gray-300">/</span>
            <span class="font-medium text-gray-900 dark:text-white">Reports</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="flex flex-col gap-1">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Transaction & Attendance
                        Report</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Filter and analyze service usage and revenue data
                        across all gym stations.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <button onclick="window.print()"
                        class="flex items-center justify-center gap-2 px-4 py-2 bg-white dark:bg-[#253341] border border-gray-200 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-200 font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm w-full sm:w-auto">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span>Print</span>
                    </button>
                    <a href="{{ route('admin.reports.export', request()->query()) }}"
                        class="flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-md font-semibold transition-colors shadow-sm shadow-blue-200 dark:shadow-none w-full sm:w-auto text-center">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        <span>Export CSV</span>
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-surface-dark p-5 rounded-md shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col gap-1">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Period Revenue</span>
                        <span
                            class="p-1.5 bg-green-100 dark:bg-green-900/30 rounded-md text-green-600 dark:text-green-400">
                            <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">RWF
                        {{ number_format($stats['total_revenue']) }}
                    </h3>
                    <div class="mt-2 space-y-1">
                        <div class="flex justify-between text-[10px] text-gray-500 font-medium">
                            <span>Desk Collections:</span>
                            <span>RWF {{ number_format($stats['shift_revenue']) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] text-gray-500 font-medium">
                            <span>Ticket Sales:</span>
                            <span>RWF {{ number_format($stats['ticket_revenue']) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] text-gray-500 font-medium">
                            <span>Subscriptions:</span>
                            <span>RWF {{ number_format($stats['subscription_revenue']) }}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark p-5 rounded-md shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col gap-1">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Check-ins</span>
                        <span class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-md text-primary">
                            <span class="material-symbols-outlined text-[20px]">groups</span>
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($stats['total_checkins']) }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sum of user counts</p>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark p-5 rounded-md shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col gap-1">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Station Filter</span>
                        <span
                            class="p-1.5 bg-purple-100 dark:bg-purple-900/30 rounded-md text-purple-600 dark:text-purple-400">
                            <span class="material-symbols-outlined text-[20px]">location_on</span>
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-2">
                        {{ ($filters['station_id'] ?? null) ? $stations->find($filters['station_id'])->name : 'All Stations' }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Visibility Scope</p>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark p-5 rounded-md shadow-sm border border-gray-200 dark:border-gray-800 flex flex-col gap-1">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">Digital Payments</span>
                        <span
                            class="p-1.5 bg-orange-100 dark:bg-orange-900/30 rounded-md text-orange-600 dark:text-orange-400">
                            <span class="material-symbols-outlined text-[20px]">smartphone</span>
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['momo_percent'] }}%</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Mobile Money adoption</p>
                </div>
            </div>

            <!-- Filter Section -->
            <div
                class="bg-white dark:bg-surface-dark rounded-md border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">filter_list</span>
                        Filter Criteria
                    </h3>
                    <a href="{{ route('admin.reports') }}"
                        class="text-sm text-primary hover:text-blue-700 font-semibold">Clear All</a>
                </div>
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.reports') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="flex flex-col gap-1.5">
                                <label
                                    class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date
                                    From</label>
                                <input name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-sm text-gray-900 dark:text-white focus:ring-primary focus:border-primary focus:outline-none"
                                    type="date" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label
                                    class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date
                                    To</label>
                                <input name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                    class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-sm text-gray-900 dark:text-white focus:ring-primary focus:border-primary focus:outline-none"
                                    type="date" />
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label
                                    class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Station</label>
                                <select name="station_id"
                                    class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-sm text-gray-900 dark:text-white focus:ring-primary focus:border-primary focus:outline-none">
                                    <option value="">All Stations</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}" {{ ($filters['station_id'] ?? '') == $station->id ? 'selected' : '' }}>{{ $station->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label
                                    class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment
                                    Method</label>
                                <select name="payment_method"
                                    class="w-full py-2 px-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded text-sm text-gray-900 dark:text-white focus:ring-primary focus:border-primary focus:outline-none">
                                    <option value="">All Methods</option>
                                    <option value="Cash" {{ ($filters['payment_method'] ?? '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Mobile" {{ ($filters['payment_method'] ?? '') == 'Mobile' ? 'selected' : '' }}>Mobile Money</option>
                                    <option value="Signature" {{ ($filters['payment_method'] ?? '') == 'Signature' ? 'selected' : '' }}>Institution / Signing</option>
                                    <option value="Ticket" {{ ($filters['payment_method'] ?? '') == 'Ticket' ? 'selected' : '' }}>Ticket</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <button type="submit"
                                class="px-5 py-2 text-sm font-bold text-white bg-primary rounded hover:bg-primary-hover transition-colors shadow-sm w-full md:w-auto">
                                Apply Analysis
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div
                class="bg-white dark:bg-surface-dark rounded-md border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col">
                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/30">
                    <h3 class="font-bold text-gray-900 dark:text-white">Filtered Results <span
                            class="ml-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-white dark:bg-gray-700 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-600">{{ $logs->total() }}
                            records</span></h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead
                            class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-4">Date & Time</th>
                                <th class="px-6 py-4 hidden md:table-cell">Receptionist</th>
                                <th class="px-6 py-4">Service</th>
                                <th class="px-6 py-4 text-right hidden lg:table-cell">Collection Price</th>
                                <th class="px-6 py-4 hidden md:table-cell">Station</th>
                                <th class="px-6 py-4 hidden sm:table-cell">Category</th>
                                <th class="px-6 py-4 text-center">Users (Qty)</th>
                                <th class="px-6 py-4 text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                        {{ $log->created_at->format('M d, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold hidden md:table-cell">
                                        {{ $log->user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $log->service->name }}</span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-600 hidden lg:table-cell">
                                        {{ number_format($log->unit_price ?? $log->service->price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 hidden md:table-cell">
                                        {{ $log->station->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-gray-400 text-[18px]">
                                                {{ $log->payment_method === 'Cash' ? 'payments' : ($log->payment_method === 'Ticket' ? 'confirmation_number' : ($log->payment_method === 'Signature' ? 'edit_square' : 'smartphone')) }}
                                            </span>
                                            {{ $log->payment_method === 'Signature' ? 'Institution' : ($log->payment_method === 'Mobile' ? 'Momo' : $log->payment_method) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $log->user_count }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right font-bold text-gray-900 dark:text-white">
                                        RWF {{ number_format($log->amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-tight">
                        Displaying <span class="font-bold text-gray-900 dark:text-white">{{ $logs->firstItem() ?? 0 }} -
                            {{ $logs->lastItem() ?? 0 }}</span> of {{ $logs->total() }} entries
                    </p>
                    <div>
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>