<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Reception Dashboard</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if($assignment)
                    <div class="hidden md:flex flex-col items-end mr-2">
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Current
                            Assignment</span>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-sm font-bold text-gray-900 dark:text-white">{{ $assignment->station->name }}</span>
                            <span
                                class="text-xs text-gray-500 whitespace-nowrap">({{ \Carbon\Carbon::parse($assignment->start_time)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($assignment->end_time)->format('H:i') }})</span>
                        </div>
                    </div>
                @endif
                <a href="{{ route('receptionist.entry') }}"
                    class="flex items-center justify-center h-10 px-4 rounded-md bg-primary text-white text-sm font-semibold hover:bg-primary-hover transition shadow-sm shadow-indigo-200 dark:shadow-none">
                    <span class="material-symbols-outlined text-[20px] mr-2">edit_square</span>
                    New Entry
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Actions Card -->
        <div class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
            <div class="space-y-4">
                <a href="{{ route('receptionist.entry') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-all bg-gray-50 dark:bg-gray-800 hover:bg-white dark:hover:bg-gray-700 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">add_circle</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Record Attendance</p>
                            <p class="text-sm text-gray-500">Log a new gym visit or service</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined text-gray-400 group-hover:text-primary transition-colors">arrow_forward</span>
                </a>

                <a href="{{ route('receptionist.summary') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-all bg-gray-50 dark:bg-gray-800 hover:bg-white dark:hover:bg-gray-700 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <span class="material-symbols-outlined">summarize</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Shift Summary</p>
                            <p class="text-sm text-gray-500">View your current shift stats</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined text-gray-400 group-hover:text-primary transition-colors">arrow_forward</span>
                </a>

                <a href="{{ route('receptionist.assignments') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary dark:hover:border-primary transition-all bg-gray-50 dark:bg-gray-800 hover:bg-white dark:hover:bg-gray-700 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div
                            class="size-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                            <span class="material-symbols-outlined">assignment_ind</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">Assign Staff</p>
                            <p class="text-sm text-gray-500">Assign clients to therapists</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined text-gray-400 group-hover:text-primary transition-colors">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Recent Activity</h3>
            @if(isset($activities) && count($activities) > 0)
                <ul class="space-y-4">
                    @foreach($activities as $activity)
                        <li
                            class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-gray-800 last:border-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <div
                                    class="size-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                                    <span class="material-symbols-outlined text-[18px]">person</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['customer'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['service'] }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $activity['revenue'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="flex flex-col items-center justify-center h-40 text-center">
                    <span class="material-symbols-outlined text-gray-300 text-4xl mb-2">history</span>
                    <p class="text-gray-500">No recent activity</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>