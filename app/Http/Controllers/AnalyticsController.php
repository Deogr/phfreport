<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\ShiftReport;
use App\Models\Station;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Unified Revenue Trends (Last 30 Days)
        $days = 30;
        $startDate = now()->subDays($days)->startOfDay();

        $shiftRevenue = ShiftReport::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_revenue) as total')
        )
            ->where('status', 'approved')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $ticketRevenue = \App\Models\Ticket::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
            ->where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $subscriptionRevenue = \App\Models\Subscription::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(price) as total')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        // Merge and Fill Gaps
        $revenueTrends = collect();
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $total = ($shiftRevenue->get($date) ?? 0) +
                ($ticketRevenue->get($date) ?? 0) +
                ($subscriptionRevenue->get($date) ?? 0);

            $revenueTrends->push((object) [
                'date' => Carbon::parse($date)->format('M d'),
                'total' => $total
            ]);
        }

        // 2. Attendance by Station (Pie Chart)
        $stationAttendance = AttendanceLog::select('station_id', DB::raw('SUM(user_count) as total'))
            ->groupBy('station_id')
            ->with('station')
            ->get()
            ->map(function ($log) {
                return [
                    'label' => $log->station->name ?? 'Unknown',
                    'value' => (int) $log->total
                ];
            });

        // 3. Service Popularity (Bar Chart)
        $servicePopularity = AttendanceLog::select('service_id', DB::raw('SUM(user_count) as total'))
            ->groupBy('service_id')
            ->with('service')
            ->get()
            ->map(function ($log) {
                return [
                    'label' => $log->service->name ?? 'Unknown',
                    'value' => (int) $log->total
                ];
            });

        // 4. Peak Hours Analysis (Line Chart)
        $peakHours = AttendanceLog::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('SUM(user_count) as total')
        )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return view('shared.analytics', [
            'revenueTrends' => $revenueTrends,
            'stationAttendance' => $stationAttendance,
            'servicePopularity' => $servicePopularity,
            'peakHours' => $peakHours
        ]);
    }
}
