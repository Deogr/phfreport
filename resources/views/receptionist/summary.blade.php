<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shift Summary') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @if(session('rejection_reason'))
                    <div class="md:col-span-3 lg:col-span-6 bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <span class="material-symbols-outlined text-red-500">warning</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <strong>Previous Report Rejected:</strong> {{ session('rejection_reason') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Summary Cards -->
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow sm:p-6 border border-gray-200 dark:border-gray-700">
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Revenue
                    </dt>
                    <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($summary['total_revenue']) }}
                    </dd>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow sm:p-6 border border-gray-200 dark:border-gray-700">
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Cash In Hand</dt>
                    <dd class="mt-1 text-2xl font-bold text-green-600">{{ number_format($summary['total_cash']) }}
                    </dd>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow sm:p-6 border border-gray-200 dark:border-gray-700">
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Mobile Money</dt>
                    <dd class="mt-1 text-2xl font-bold text-blue-600">{{ number_format($summary['total_momo']) }}
                    </dd>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow sm:p-6 border border-gray-200 dark:border-gray-700">
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Subs / Instit.
                    </dt>
                    <dd class="mt-1 text-2xl font-bold text-indigo-600">{{ $summary['institution_users'] }}
                    </dd>
                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-tighter">User Count</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow sm:p-6 border border-gray-200 dark:border-gray-700">
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Ticket Users</dt>
                    <dd class="mt-1 text-2xl font-bold text-orange-600">{{ $summary['ticket_users'] }}
                    </dd>
                    <p class="text-[8px] font-medium text-gray-400 uppercase tracking-tighter">User Count</p>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow sm:p-6 border border-gray-200 dark:border-gray-700">
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Attendance
                    </dt>
                    <dd class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_users'] }}
                    </dd>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Service Breakdown</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Gym</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $summary['gym_count'] }} users</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Sauna Only</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $summary['sauna_count'] }} users</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Massage Only</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $summary['massage_count'] }} users
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Sauna & Massage Combo</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $summary['combo_count'] }} users</dd>
                    </div>
                </dl>
            </div>

            <!-- Logs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Detailed Shift Logs</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Service</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Collection Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Users</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($logs as $log)
                                <tr>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        <div class="flex flex-col">
                                            <span>{{ $log->service->name }}</span>
                                            @if($log->subscription)
                                                <span
                                                    class="text-[10px] text-indigo-500 font-bold uppercase tracking-tighter">Sub:
                                                    {{ $log->subscription->guest_name }}</span>
                                            @elseif($log->ticketItem)
                                                <span
                                                    class="text-[10px] text-orange-500 font-bold uppercase tracking-tighter">Ticket:
                                                    {{ $log->ticketItem->ticket->guest_name }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-bold">
                                        {{ number_format($log->unit_price ?? $log->service->price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->user_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->payment_method }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                        {{ number_format($log->amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->created_at->format('H:i') }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-3 items-center">
                                        <a href="{{ route('receptionist.logs.edit', $log) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-bold text-xs uppercase tracking-wide">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('receptionist.logs.destroy', $log) }}"
                                            id="delete-log-{{ $log->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="$dispatch('open-confirmation', { 
                                                            title: 'Delete Entry?', 
                                                            message: 'Are you sure you want to remove this entry? This cannot be undone.', 
                                                            type: 'danger', 
                                                            confirmText: 'Yes, Delete', 
                                                            formId: 'delete-log-{{ $log->id }}' 
                                                        })"
                                                class="text-red-600 hover:text-red-900 font-bold text-xs uppercase tracking-wide">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Finalize Action -->
            <div class="flex justify-end">
                <form method="POST" action="{{ route('receptionist.finalize') }}" id="finalize-shift-form">
                    @csrf
                    <x-primary-button type="button" @click="$dispatch('open-confirmation', { 
                            title: 'Finalize Shift?', 
                            message: 'This will submit the report for review. You cannot edit entries afterwards.', 
                            type: 'info', 
                            confirmText: 'Submit Report', 
                            formId: 'finalize-shift-form' 
                        })" class="bg-red-600 hover:bg-red-500 text-lg py-3 px-6">
                        {{ __('Finalize Shift & Submit Report') }}
                    </x-primary-button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>