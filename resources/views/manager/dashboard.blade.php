<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Manager Dashboard</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Overview for {{ now()->format('F j, Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('manager.review') }}"
                    class="flex items-center justify-center h-10 px-4 rounded-md bg-white dark:bg-[#253341] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <span class="material-symbols-outlined text-[20px] mr-2">rate_review</span>
                    Review Reports
                </a>
                <a href="{{ route('manager.assign') }}"
                    class="flex items-center justify-center h-10 px-4 rounded-md bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition shadow-sm shadow-indigo-200 dark:shadow-none">
                    <span class="material-symbols-outlined text-[20px] mr-2">person_add</span>
                    Assign Staff
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pending Approvals -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Approvals</p>
                        <div
                            class="bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['pendingApprovals'] }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        @if($stats['pendingApprovals'] > 0)
                            <span class="text-xs text-yellow-500 font-medium">Action required</span>
                        @else
                            <span class="text-xs text-green-500 font-medium">All caught up</span>
                        @endif
                    </div>
                </div>

                <!-- Active Subscriptions -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Subscriptions</p>
                        <div
                            class="bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">card_membership</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['activeSubscriptions'] }}
                    </p>
                    <div class="flex flex-col gap-1 mt-1">
                        <a href="{{ route('manager.subscriptions.index') }}"
                            class="text-xs text-primary font-medium hover:underline">Manage Subscriptions</a>
                        @if($stats['expiringSubscriptions'] > 0)
                            <span class="text-xs text-red-500 font-medium">{{ $stats['expiringSubscriptions'] }} expiring
                                soon</span>
                        @endif
                    </div>
                </div>

                <!-- Tickets Sold -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tickets Sold</p>
                        <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['ticketsSold'] }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <a href="{{ route('manager.tickets') }}"
                            class="text-xs text-primary font-medium hover:underline">Manage Sales</a>
                    </div>
                </div>

                <!-- Revenue Today -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue Today</p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">payments</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">RWF
                        {{ number_format($stats['revenue']['current']) }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-xs text-gray-400">Target: RWF
                            {{ number_format($stats['revenue']['target']) }}</span>
                    </div>
                </div>

                <!-- Active Members -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Members</p>
                        <div
                            class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">groups</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ number_format($stats['membership']['active']) }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-green-500 text-sm">check_circle</span>
                        <span class="text-xs text-gray-400">{{ $stats['membership']['checkins'] }} check-ins
                            today</span>
                    </div>
                </div>

                <!-- Staff On Duty -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Staff On Duty</p>
                        <div class="bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">badge</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['membership']['staffOnDuty'] }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-xs text-green-500 font-medium">Fully staffed</span>
                    </div>
                </div>
            </div>

            <!-- Pending Reports Section (High Visibility) -->
            @if($pendingReportsList->count() > 0)
                <div
                    class="bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-900/30 rounded-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-orange-900 dark:text-orange-400 flex items-center gap-2">
                            <span class="material-symbols-outlined">notification_important</span>
                            Reports Awaiting Approval ({{ $pendingReportsList->count() }})
                        </h3>
                        <a href="{{ route('manager.review') }}"
                            class="text-sm font-bold text-orange-700 dark:text-orange-500 hover:underline">Process Now</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($pendingReportsList as $report)
                            <div
                                class="bg-white dark:bg-gray-800 p-4 rounded border border-orange-100 dark:border-orange-900/20 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase">{{ $report->station->name }}</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $report->receptionist->name }}
                                        </p>
                                    </div>
                                    <p class="text-sm font-bold text-primary">RWF {{ number_format($report->total_revenue) }}
                                    </p>
                                </div>
                                <div class="mt-3 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-2 rounded">
                                    <p class="text-[10px] text-gray-500">{{ $report->created_at->diffForHumans() }}</p>
                                    <a href="{{ route('manager.review') }}"
                                        class="text-[10px] font-bold text-primary uppercase tracking-widest">Review</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <!-- Managerial Controls (Left) -->
                <div class="lg:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Managerial Controls
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('manager.assign') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px] text-primary">calendar_month</span>
                                <span class="text-sm font-medium">Duty Scheduling</span>
                            </a>
                            <a href="{{ route('manager.review') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300 relative">
                                <span class="material-symbols-outlined text-[20px] text-primary">rate_review</span>
                                <span class="text-sm font-medium">Review Shift Reports</span>
                                @if($stats['pendingApprovals'] > 0)
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 size-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $stats['pendingApprovals'] }}</span>
                                @endif
                            </a>
                            <a href="{{ route('manager.subscriptions.index') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px] text-primary">card_membership</span>
                                <span class="text-sm font-medium">Subscription Desk</span>
                            </a>
                            <a href="{{ route('manager.tickets') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span
                                    class="material-symbols-outlined text-[20px] text-primary">confirmation_number</span>
                                <span class="text-sm font-medium">Ticket Sales</span>
                            </a>
                            <a href="{{ route('manager.analytics') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px] text-primary">analytics</span>
                                <span class="text-sm font-medium">Managerial Analytics</span>
                            </a>
                        </div>
                    </div>

                    <!-- Revenue Target Widget -->
                    <div
                        class="bg-primary p-6 rounded-md shadow-lg shadow-primary/20 text-white overflow-hidden relative group">
                        <div class="relative z-10">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Collection Progress
                            </p>
                            <h4 class="text-xl font-bold mt-1">RWF {{ number_format($stats['revenue']['current']) }}
                            </h4>
                            <div class="mt-4">
                                <div class="flex justify-between text-xs mb-1 opacity-90">
                                    <span>Goal: {{ number_format($stats['revenue']['target']) }}</span>
                                    <span>{{ round(($stats['revenue']['current'] / $stats['revenue']['target']) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-white/20 rounded-full h-1.5">
                                    <div class="bg-white h-1.5 rounded-full"
                                        style="width: {{ ($stats['revenue']['current'] / $stats['revenue']['target']) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Station Revenue Today -->
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary text-[18px]">location_on</span>
                             Station Performance
                        </h3>
                        <div class="space-y-4">
                            @foreach($stationRevenue as $stat)
                                <div class="flex items-center justify-between p-3 bg-gray-50/50 dark:bg-gray-800/30 rounded-lg border border-gray-100 dark:border-gray-800">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $stat['name'] }}</span>
                                        <span class="text-[9px] text-gray-400 uppercase tracking-widest font-bold">{{ $stat['count'] }} Shifts Today</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5 tracking-tighter">Collection</p>
                                        <p class="text-sm font-black text-primary">RWF {{ number_format($stat['revenue']) }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if($stationRevenue->isEmpty())
                                <p class="text-center text-xs text-gray-400 italic py-2">No shift revenue today.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Live Activity Feed (Right) -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Live Activity stream</h3>
                        <span
                            class="text-[10px] items-center text-green-500 font-bold bg-green-500/10 px-2 py-0.5 rounded-full flex gap-1">
                            <span class="size-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            ACTIVE
                        </span>
                    </div>
                    <div class="space-y-4">
                        @foreach($unifiedActivity as $activity)
                            <div
                                class="flex items-start gap-4 p-4 rounded-lg border border-gray-50 dark:border-gray-800/50 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="size-10 rounded-full flex items-center justify-center shrink-0 
                                        @if($activity['color'] == 'blue') bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400
                                        @elseif($activity['color'] == 'purple') bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400
                                        @elseif($activity['color'] == 'orange') bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400
                                        @else bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 @endif">
                                    <span class="material-symbols-outlined text-[20px]">{{ $activity['icon'] }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity['title'] }}
                                        </h4>
                                        <span
                                            class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity['amount'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $activity['subtitle'] }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">
                                        {{ $activity['time']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-50 dark:border-gray-800">
                        <a href="{{ route('manager.review') }}"
                            class="text-xs font-bold text-primary hover:underline flex items-center justify-center gap-1">
                            VIEW GLOBAL FINANCIAL LOGS
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>