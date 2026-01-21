<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Shift History') }}
            </h2>
            <form method="GET" action="{{ route('receptionist.history') }}" class="flex items-center gap-2">
                <input type="month" name="month" value="{{ $filters['month'] ?? date('Y-m') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm">
                <x-secondary-button type="submit" class="h-[38px]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                </x-secondary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm sm:rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="p-6">
                    @if($reports->isEmpty())
                        <div class="text-center py-12">
                            <span
                                class="material-symbols-outlined text-gray-300 dark:text-gray-700 text-6xl mb-4">history</span>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">No shift reports found for this period.
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Date</th>
                                        <th
                                            class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest hidden sm:table-cell">
                                            Station</th>
                                        <th
                                            class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Revenue</th>
                                        <th
                                            class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest hidden md:table-cell">
                                            Details</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-900/50">
                                    @foreach($reports as $report)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="font-bold text-gray-900 dark:text-white">{{ $report->created_at->format('M d, Y') }}</span>
                                                    <span
                                                        class="text-[10px] text-gray-500">{{ $report->created_at->format('H:i') }}
                                                        -
                                                        {{ $report->end_time ? $report->end_time->format('H:i') : 'Active' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                                <span
                                                    class="text-sm text-gray-600 dark:text-gray-400">{{ $report->station->name }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex flex-col">
                                                    <div>
                                                        <span
                                                            class="font-bold text-gray-900 dark:text-white">{{ number_format($report->total_revenue) }}</span>
                                                        <span class="text-[10px] text-gray-400 ml-1 hidden sm:inline">RWF</span>
                                                    </div>
                                                    <div class="flex justify-end gap-2 mt-1">
                                                        @if($report->total_tickets > 0)
                                                            <span
                                                                class="text-[9px] font-bold text-orange-600 bg-orange-50 px-1.5 rounded"
                                                                title="Total Ticket Users">T: {{ $report->total_tickets }}</span>
                                                        @endif
                                                        @if($report->total_subscriptions > 0)
                                                            <span
                                                                class="text-[9px] font-bold text-indigo-600 bg-indigo-50 px-1.5 rounded"
                                                                title="Total Subscription Users">S:
                                                                {{ $report->total_subscriptions }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @php
                                                    $statusColor = match ($report->status) {
                                                        'submitted' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/30',
                                                        'approved' => 'bg-green-50 text-green-700 border-green-100 dark:bg-green-900/20 dark:text-green-400 dark:border-green-900/30',
                                                        'rejected' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/30',
                                                        default => 'bg-gray-50 text-gray-700 border-gray-100 dark:bg-gray-900/20 dark:text-gray-400 dark:border-gray-900/30',
                                                    };
                                                @endphp
                                                <div class="flex flex-col items-center gap-1">
                                                    <span
                                                        class="px-2.5 py-1 rounded border {{ $statusColor }} text-[10px] font-bold uppercase tracking-wider">
                                                        {{ $report->status }}
                                                    </span>
                                                    @if($report->status === 'rejected' && $report->rejection_reason)
                                                        <span
                                                            class="text-[9px] text-red-500 italic max-w-[150px] truncate hidden sm:block"
                                                            title="{{ $report->rejection_reason }}">
                                                            Reason: {{ $report->rejection_reason }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center hidden md:table-cell">
                                                <button type="button"
                                                    class="p-1 px-3 rounded-full text-[10px] font-bold uppercase tracking-widest bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-primary hover:text-white transition-all shadow-sm">
                                                    View Logs
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>