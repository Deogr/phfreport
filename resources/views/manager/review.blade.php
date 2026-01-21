<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Review Shift Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($reports->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">No submitted reports pending review.</p>
                @else
                    <div class="space-y-6">
                        @foreach($reports as $report)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            Shift at {{ $report->station->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Submitted by {{ $report->receptionist->name }} on
                                            {{ $report->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold dark:text-white">
                                            {{ number_format($report->total_revenue) }} RWF
                                        </div>
                                        <div class="text-xs text-gray-500">Total Revenue</div>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded">
                                        <span class="block text-gray-500">Cash Breakdown</span>
                                        <span class="font-bold dark:text-gray-200">{{ number_format($report->total_cash) }}
                                            RWF</span>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded">
                                        <span class="block text-gray-500">Momo Breakdown</span>
                                        <span class="font-bold dark:text-gray-200">{{ number_format($report->total_momo) }}
                                            RWF</span>
                                    </div>
                                </div>

                                <!-- Detailed Log Breakdown -->
                                <div class="mt-6">
                                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Itemized Entries
                                    </h4>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                                <tr>
                                                    <th
                                                        class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">
                                                        Service</th>
                                                    <th
                                                        class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">
                                                        Price</th>
                                                    <th
                                                        class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase text-center">
                                                        Qty</th>
                                                    <th
                                                        class="px-3 py-2 text-left text-[10px] font-bold text-gray-500 uppercase">
                                                        Method</th>
                                                    <th
                                                        class="px-3 py-2 text-right text-[10px] font-bold text-gray-500 uppercase">
                                                        Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                @foreach($report->attendanceLogs as $log)
                                                    <tr>
                                                        <td class="px-3 py-2 text-xs font-medium text-gray-900 dark:text-white">
                                                            {{ $log->service->name }}
                                                        </td>
                                                        <td class="px-3 py-2 text-xs text-gray-500">
                                                            {{ number_format($log->unit_price ?? $log->service->price) }}
                                                        </td>
                                                        <td class="px-3 py-2 text-xs text-gray-500 text-center">
                                                            {{ $log->user_count }}
                                                        </td>
                                                        <td class="px-3 py-2 text-xs">
                                                            <span
                                                                class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $log->payment_method === 'Mobile' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                                                {{ $log->payment_method }}
                                                            </span>
                                                        </td>
                                                        <td
                                                            class="px-3 py-2 text-xs font-bold text-right text-gray-900 dark:text-white">
                                                            {{ number_format($log->amount) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700 pt-4">
                                    <!-- Approve Form -->
                                    <form method="POST"
                                        action="{{ route(auth()->user()->role === 'admin' ? 'admin.reports.approve' : 'manager.reports.approve', $report) }}">
                                        @csrf
                                        <x-primary-button class="bg-green-600 hover:bg-green-500">
                                            {{ __('Approve') }}
                                        </x-primary-button>
                                    </form>

                                    <!-- Reject Button (opens modal ideally, simpler here) -->
                                    <form method="POST" action="{{ route(auth()->user()->role === 'admin' ? 'admin.reports.reject' : 'manager.reports.reject', $report) }}"
                                        class="flex gap-2 items-center">
                                        @csrf
                                        <x-text-input name="reason" placeholder="Reason (optional)" class="text-xs py-1" />
                                        <x-danger-button>
                                            {{ __('Reject') }}
                                        </x-danger-button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>