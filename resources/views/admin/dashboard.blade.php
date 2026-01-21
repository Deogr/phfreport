<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Overview</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Updates for {{ now()->format('F j, Y') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <a href="{{ route('admin.reviews') }}"
                    class="flex items-center justify-center h-10 px-4 rounded-md bg-white dark:bg-[#253341] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[20px] mr-2">rate_review</span>
                    Review Reports
                </a>
                <a href="{{ route('admin.reports') }}"
                    class="flex items-center justify-center h-10 px-4 rounded-md bg-primary text-white text-sm font-semibold hover:bg-blue-600 transition shadow-sm shadow-blue-200 dark:shadow-none w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[20px] mr-2">monitoring</span>
                    Financial Reports
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPI Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Revenue -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue Today</p>
                        <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">payments</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">RWF
                        {{ number_format($stats['revenue']['today']) }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-green-500 text-sm">trending_up</span>
                        <span class="text-sm font-medium text-green-600">{{ $stats['revenue']['trend'] }}</span>
                        <span class="text-xs text-gray-400 ml-1">vs last week</span>
                    </div>
                </div>

                <!-- Attendance -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Daily Attendance</p>
                        <div
                            class="bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">groups</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['attendance']['today'] }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="material-symbols-outlined text-green-500 text-sm">trending_up</span>
                        <span class="text-sm font-medium text-green-600">+12%</span>
                        <span class="text-xs text-gray-400 ml-1">vs yesterday</span>
                    </div>
                </div>

                <!-- Active Stations -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Stations</p>
                        <div
                            class="bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">settings_input_component</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['stations']['active'] }}/{{ $stats['stations']['total'] }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-xs text-gray-400">All systems operational</span>
                    </div>
                </div>

                <!-- Staff On Audit -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col gap-1">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Reviews</p>
                        <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded p-1">
                            <span class="material-symbols-outlined text-[20px]">notification_important</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                        {{ $stats['pending_audits'] }}
                    </p>
                    <div class="flex items-center gap-1 mt-1">
                        @if($stats['pending_audits'] > 0)
                            <span class="text-xs text-red-500 font-medium">Attention needed</span>
                        @else
                            <span class="text-xs text-green-500 font-medium">None pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reports Section (High Visibility) -->
            @if($pendingReportsList->count() > 0)
                <div
                    class="bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-900/30 rounded-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-orange-900 dark:text-orange-400 flex items-center gap-2">
                            <span class="material-symbols-outlined">rate_review</span>
                            Shift Reports Awaiting Audit ({{ $pendingReportsList->count() }})
                        </h3>
                        <a href="{{ route('admin.reviews') }}"
                            class="text-sm font-bold text-orange-700 dark:text-orange-500 hover:underline">View All</a>
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
                                    <a href="{{ route('admin.reviews') }}"
                                        class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Review
                                        & Audit</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User/System Management Panel (Left Sidebar style or Top Grid) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Management Actions -->
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">admin_panel_settings</span>
                            System Management
                        </h3>
                        <div class="grid grid-cols-1 gap-2">
                            <a href="{{ route('admin.users') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                                <span class="text-sm font-medium">System User Management</span>
                            </a>
                            <a href="{{ route('admin.stations') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">settings_input_component</span>
                                <span class="text-sm font-medium">Branch/Station Control</span>
                            </a>
                            <a href="{{ route('admin.services') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">list_alt</span>
                                <span class="text-sm font-medium">Service & Price Catalog</span>
                            </a>
                            <a href="{{ route('admin.assignments') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                                <span class="text-sm font-medium">Duty Assignments</span>
                            </a>
                            <a href="{{ route('admin.subscriptions.index') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">card_membership</span>
                                <span class="text-sm font-medium">Subscription Management</span>
                            </a>
                            <a href="{{ route('admin.tickets') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                                <span class="text-sm font-medium">Ticket Inventory & Sales</span>
                            </a>
                            <hr class="my-2 border-gray-100 dark:border-gray-800">
                            <a href="{{ route('admin.logs') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">vitals</span>
                                <span class="text-sm font-medium">System Audit Logs</span>
                            </a>
                            <a href="{{ route('admin.reports') }}"
                                class="flex items-center gap-3 p-3 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition text-gray-700 dark:text-gray-300">
                                <span class="material-symbols-outlined text-[20px]">monitoring</span>
                                <span class="text-sm font-medium">Full Financial Intelligence</span>
                            </a>
                        </div>
                    </div>

                    <!-- Usage Stats -->
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Service Popularity</h3>
                        <div class="space-y-4">
                            @foreach($serviceUsage as $service)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-xs font-medium text-gray-500">{{ $service['name'] }}</span>
                                        <span
                                            class="text-xs font-bold text-gray-900 dark:text-white">{{ $service['value'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full"
                                            style="width: {{ $service['value'] }}%; background-color: {{ $service['color'] }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Station Performance -->
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm transition hover:shadow-md">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                             <span class="material-symbols-outlined text-primary text-[18px]">location_on</span>
                             Station Revenue Today
                        </h3>
                        <div class="space-y-3">
                            @foreach($stationRevenue as $stat)
                                <div class="flex items-center justify-between p-3 bg-gray-50/50 dark:bg-gray-800/30 rounded-lg border border-gray-100 dark:border-gray-800 group hover:border-primary/30 transition-colors">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $stat['name'] }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="size-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            <span class="text-[9px] text-gray-400 uppercase tracking-widest font-bold">{{ $stat['count'] }} Approved Shifts</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-400 uppercase font-bold mb-0.5 tracking-tighter">Collection</p>
                                        <p class="text-sm font-black text-primary">RWF {{ number_format($stat['revenue']) }}</p>
                                    </div>
                                </div>
                            @endforeach
                            @if($stationRevenue->isEmpty())
                                <div class="text-center py-4 px-2 bg-gray-50 dark:bg-gray-800/20 rounded-lg border border-dashed border-gray-200 dark:border-gray-800">
                                    <span class="material-symbols-outlined text-gray-300 dark:text-gray-700 text-3xl mb-1">finance_chip</span>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">No Station Revenue Yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Center Content: Revenue Chart & Unified Feed -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Revenue Chart Area -->
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Revenue Analysis</h3>
                                <p class="text-sm text-gray-500">Gross income collection over time (All Sources)</p>
                            </div>
                        </div>
                        @php $maxRevenue = collect($revenueHistory)->max('revenue') ?: 1; @endphp
                        <div
                            class="h-64 flex items-end justify-between gap-2 md:gap-4 w-full pt-4 border-b border-gray-100 dark:border-gray-700 relative">
                            <div
                                class="absolute inset-0 flex flex-col justify-between text-xs text-gray-400 pointer-events-none pb-8 pl-1">
                                <div class="w-full border-t border-dashed border-gray-200 dark:border-gray-800"></div>
                                <div class="w-full border-t border-dashed border-gray-200 dark:border-gray-800"></div>
                                <div class="w-full border-t border-dashed border-gray-200 dark:border-gray-800"></div>
                                <div class="w-full border-t border-dashed border-gray-200 dark:border-gray-800"></div>
                            </div>
                            @foreach($revenueHistory as $record)
                                <div class="w-full bg-primary/20 hover:bg-primary transition-all rounded-t-sm relative group"
                                    style="height: {{ ($record['revenue'] / $maxRevenue * 90) + 5 }}%">
                                    <div
                                        class="opacity-0 group-hover:opacity-100 absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap z-20 pointer-events-none transition-opacity">
                                        RWF {{ number_format($record['revenue']) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Unified System Activity -->
                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <h3
                                class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-wider text-xs">
                                Global Activity Stream</h3>
                            <span
                                class="flex items-center gap-1 text-[10px] items-center text-green-500 font-bold bg-green-500/10 px-2 py-0.5 rounded-full">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                LIVE
                            </span>
                        </div>
                        <div class="space-y-4">
                            @foreach($unifiedActivity as $activity)
                                <div
                                    class="flex items-start gap-4 p-3 rounded-lg border border-gray-50 dark:border-gray-800 hover:border-gray-100 dark:hover:border-gray-700 transition">
                                    <div
                                        class="size-10 rounded-full flex items-center justify-center shrink-0 
                                            @if($activity['color'] == 'blue') bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400
                                            @elseif($activity['color'] == 'purple') bg-purple-50 text-purple-600 dark:bg-purple-900/20 dark:text-purple-400
                                            @elseif($activity['color'] == 'orange') bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400
                                            @else bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 @endif">
                                        <span class="material-symbols-outlined text-[20px]">{{ $activity['icon'] }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                                {{ $activity['title'] }}</h4>
                                            <span
                                                class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity['amount'] }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $activity['subtitle'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase tracking-tighter">
                                            {{ $activity['time']->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>