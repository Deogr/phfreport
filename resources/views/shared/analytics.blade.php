<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Performance Analytics</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Detailed visual reports for the last 30 days</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="flex items-center justify-center h-10 px-4 rounded-md bg-white dark:bg-[#253341] border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition shadow-sm">
                    <span class="material-symbols-outlined text-[20px] mr-2">print</span>
                    Print Analysis
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Primary Metric: Revenue Trends -->
            <div
                class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Revenue Growth (Daily)</h3>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Attendance by Station -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Attendance Distribution</h3>
                    <div class="h-64 flex justify-center">
                        <canvas id="stationChart"></canvas>
                    </div>
                </div>

                <!-- Service Popularity -->
                <div
                    class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Service Popularity</h3>
                    <div class="h-64">
                        <canvas id="serviceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Peak Hours -->
            <div
                class="bg-white dark:bg-surface-dark p-6 rounded-md border border-gray-200 dark:border-gray-800 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Hourly Traffic Analysis</h3>
                <div class="h-64">
                    <canvas id="peakChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Chart Defaults
                Chart.defaults.color = '#94a3b8';
                Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui';

                // 1. Revenue Chart
                new Chart(document.getElementById('revenueChart'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($revenueTrends->pluck('date')) !!},
                        datasets: [{
                            label: 'Revenue (RWF)',
                            data: {!! json_encode($revenueTrends->pluck('total')) !!},
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(203, 213, 225, 0.1)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // 2. Station Attendance (Pie)
                new Chart(document.getElementById('stationChart'), {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($stationAttendance->pluck('label')) !!},
                        datasets: [{
                            data: {!! json_encode($stationAttendance->pluck('value')) !!},
                            backgroundColor: ['#4f46e5', '#818cf8', '#c7d2fe', '#e0e7ff'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' }
                        }
                    }
                });

                // 3. Service Popularity (Bar)
                new Chart(document.getElementById('serviceChart'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($servicePopularity->pluck('label')) !!},
                        datasets: [{
                            label: 'Check-ins',
                            data: {!! json_encode($servicePopularity->pluck('value')) !!},
                            backgroundColor: '#4f46e5',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(203, 213, 225, 0.1)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // 4. Peak Hours (Line)
                const hourLabels = {!! json_encode($peakHours->pluck('hour')) !!}.map(h => h + ':00');
                new Chart(document.getElementById('peakChart'), {
                    type: 'line',
                    data: {
                        labels: hourLabels,
                        datasets: [{
                            label: 'Avg Attendance',
                            data: {!! json_encode($peakHours->pluck('total')) !!},
                            borderColor: '#10b981',
                            borderWidth: 3,
                            pointBackgroundColor: '#10b981',
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(203, 213, 225, 0.1)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>