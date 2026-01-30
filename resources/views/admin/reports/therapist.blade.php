<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Therapist Performance Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Total Revenue</div>
                    <div class="text-3xl font-black text-gray-900 dark:text-white">RWF
                        {{ number_format($stats['total_revenue']) }}</div>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-1">Total Clients</div>
                    <div class="text-3xl font-black text-gray-900 dark:text-white">
                        {{ number_format($stats['total_clients']) }}</div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="date_from" :value="__('From Date')" />
                        <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full"
                            :value="$request->date_from" />
                    </div>
                    <div>
                        <x-input-label for="date_to" :value="__('To Date')" />
                        <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full"
                            :value="$request->date_to" />
                    </div>
                    <div>
                        <x-input-label for="therapist_id" :value="__('Therapist')" />
                        <select name="therapist_id" id="therapist_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Therapists</option>
                            @foreach($therapists as $therapist)
                                <option value="{{ $therapist->id }}" {{ $request->therapist_id == $therapist->id ? 'selected' : '' }}>
                                    {{ $therapist->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pb-1">
                        <x-primary-button>Filter</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($assignments->isEmpty())
                        <p class="text-center text-gray-500 py-4">No completed reports found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Therapist</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Client</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Service</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $assignment->appointment_time->format('Y-m-d H:i') }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $assignment->therapist->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $assignment->client_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $assignment->service->name }}
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900 dark:text-white">
                                                {{ number_format($assignment->final_cost) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $assignments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>