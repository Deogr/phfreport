<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daily Revenue Report') }}
            </h2>
            <form method="GET" action="{{ route('reports.daily') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm">
                <x-secondary-button type="submit" class="h-[38px]">
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                </x-secondary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-primary">payments</span>
                    </div>
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Daily
                        Revenue</dt>
                    <dd class="mt-2 text-3xl font-black text-gray-900 dark:text-white">
                        {{ number_format($totalRevenue) }} <span class="text-xs font-medium text-gray-400">RWF</span>
                    </dd>
                    <div class="mt-4 h-1 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: 100%"></div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-green-500">point_of_sale</span>
                    </div>
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Pay per Session
                    </dt>
                    <dd class="mt-2 text-3xl font-black text-green-600">
                        {{ number_format($sessionRevenue) }} <span
                            class="text-xs font-medium text-gray-400 text-green-500/50">RWF</span>
                    </dd>
                    <div class="mt-2 flex items-center justify-between text-[10px] font-bold">
                        <span class="text-gray-400 uppercase tracking-tighter">Cash:
                            {{ number_format($sessionCash) }}</span>
                        <span class="text-gray-400 uppercase tracking-tighter">Momo:
                            {{ number_format($sessionMomo) }}</span>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-orange-500">confirmation_number</span>
                    </div>
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Ticket Sales</dt>
                    <dd class="mt-2 text-3xl font-black text-orange-600">
                        {{ number_format($ticketRevenue) }} <span
                            class="text-xs font-medium text-gray-400 text-orange-500/50">RWF</span>
                    </dd>
                    <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Direct Manager Sales
                    </p>
                </div>

                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-6xl text-indigo-500">card_membership</span>
                    </div>
                    <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Sub.
                        Registrations</dt>
                    <dd class="mt-2 text-3xl font-black text-indigo-600">
                        {{ number_format($subscriptionRevenue) }} <span
                            class="text-xs font-medium text-gray-400 text-indigo-500/50">RWF</span>
                    </dd>
                    <p class="mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Direct Manager Sales
                    </p>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Station Breakdown -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="bg-white dark:bg-surface-dark rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div
                            class="px-6 py-5 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/30">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Revenue by Station</h3>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest mt-0.5">
                                    Session Collections Only</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-800">
                                <thead>
                                    <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                        <th
                                            class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Station</th>
                                        <th
                                            class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Revenue</th>
                                        <th
                                            class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                            Usage</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-900/50">
                                    @foreach($stationRevenue as $stat)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="size-8 bg-primary/10 rounded-lg flex items-center justify-center text-primary">
                                                        <span
                                                            class="material-symbols-outlined text-[18px]">location_on</span>
                                                    </div>
                                                    <span
                                                        class="font-bold text-gray-900 dark:text-white">{{ $stat['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex flex-col items-end">
                                                    <span
                                                        class="font-black text-gray-900 dark:text-white">{{ number_format($stat['total'])}}
                                                        RWF</span>
                                                    <div class="flex gap-2 text-[8px] font-bold text-gray-400 uppercase">
                                                        <span>Cash: {{ number_format($stat['cash']) }}</span>
                                                        <span>Momo: {{ number_format($stat['momo']) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex justify-center gap-2">
                                                    <span
                                                        class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-orange-50 text-orange-600 border border-orange-100"
                                                        title="Tickets Used">T: {{ $stat['tickets_used'] }}</span>
                                                    <span
                                                        class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-600 border border-indigo-100"
                                                        title="Subscriptions Used">S: {{ $stat['subs_used'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($stationRevenue->isEmpty())
                                        <tr>
                                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic text-sm">No
                                                session data for this date.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Shift List -->
                    <div
                        class="bg-white dark:bg-surface-dark rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
                        <div
                            class="px-6 py-5 border-b border-gray-50 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30">
                            <h3 class="font-bold text-gray-900 dark:text-white">Completed Shifts</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($shifts as $shift)
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-800/30 rounded-xl border border-gray-100 dark:border-gray-800 hover:border-primary/30 transition-colors group">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="size-10 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-sm font-black text-xs text-primary group-hover:scale-110 transition-transform">
                                                {{ substr($shift->receptionist->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-white leading-none">
                                                    {{ $shift->receptionist->name }}</h4>
                                                <p
                                                    class="text-[10px] text-gray-500 font-medium uppercase tracking-widest mt-1">
                                                    {{ $shift->station->name }} • {{ $shift->created_at->format('H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right font-black text-gray-900 dark:text-white">
                                            {{ number_format($shift->total_cash + $shift->total_momo) }} RWF
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-gray-400 italic text-sm">No approved shifts recorded
                                        today.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="space-y-6">
                    <div
                        class="bg-primary p-8 rounded-3xl text-white shadow-xl shadow-blue-200 dark:shadow-none relative overflow-hidden group">
                        <div class="absolute -bottom-4 -right-4 opacity-10 group-hover:scale-125 transition-transform">
                            <span class="material-symbols-outlined text-[120px]">insights</span>
                        </div>
                        <h4
                            class="text-xs font-black uppercase tracking-[0.2em] opacity-80 decoration-white/30 underline decoration-2 underline-offset-4">
                            Performance Insights</h4>
                        <div class="mt-8 space-y-6 relative">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Avg. Shift Revenue
                                </p>
                                <p class="text-2xl font-black mt-1">
                                    {{ $shifts->count() > 0 ? number_format($sessionRevenue / $shifts->count()) : 0 }}
                                    RWF
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Digital Adoption
                                    (Momo)</p>
                                <p class="text-2xl font-black mt-1">
                                    {{ $sessionRevenue > 0 ? round(($sessionMomo / $sessionRevenue) * 100) : 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white dark:bg-surface-dark p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                        <h4
                            class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">info</span>
                            About this Report
                        </h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            This report consolidates all revenue streams for the selected date. **Pay per Session**
                            revenue is sourced from receptionists' finalized and approved shift reports. **Tickets** and
                            **Subscriptions** reflect direct sales recorded in the system.
                        </p>
                        <div class="mt-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span
                                class="text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest">Data
                                is Real-time</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>