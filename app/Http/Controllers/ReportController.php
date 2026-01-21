<?php

namespace App\Http\Controllers;

use App\Models\ShiftReport;
use App\Models\Ticket;
use App\Models\Subscription;
use App\Models\Station;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : now();
        $dateStr = $date->format('Y-m-d');

        // Revenue from Shifts (Pay per Session)
        $shiftQuery = ShiftReport::whereDate('created_at', $dateStr)->whereIn('status', ['submitted', 'approved']);

        // Revenue from Direct Sales
        $ticketQuery = Ticket::whereDate('created_at', $dateStr)->where('status', 'paid');
        $subQuery = Subscription::whereDate('created_at', $dateStr);

        // Role-based filtering
        if (auth()->user()->role === 'receptionist') {
            $shiftQuery->where('user_id', auth()->id());
            // Receptionists usually don't sell tickets/subs directly in this system
            $ticketRevenue = 0;
            $subscriptionRevenue = 0;
        } else {
            // Admin/Manager see everything
            $ticketRevenue = $ticketQuery->sum('total_amount');
            $subscriptionRevenue = $subQuery->sum('price');
        }

        $shifts = $shiftQuery->with(['receptionist', 'station'])->get();

        $sessionCash = $shifts->sum('total_cash');
        $sessionMomo = $shifts->sum('total_momo');
        $sessionRevenue = $sessionCash + $sessionMomo;

        $totalRevenue = $sessionRevenue + $ticketRevenue + $subscriptionRevenue;

        // Breakdown by Station (for Session payments)
        $stationRevenue = $shifts->groupBy('station_id')->map(function ($stationShifts) {
            return [
                'name' => $stationShifts->first()->station->name ?? 'Unknown',
                'cash' => $stationShifts->sum('total_cash'),
                'momo' => $stationShifts->sum('total_momo'),
                'total' => $stationShifts->sum('total_cash') + $stationShifts->sum('total_momo'),
                'tickets_used' => $stationShifts->sum('total_tickets'),
                'subs_used' => $stationShifts->sum('total_subscriptions'),
            ];
        });

        return view('reports.daily', [
            'date' => $date,
            'sessionCash' => $sessionCash,
            'sessionMomo' => $sessionMomo,
            'sessionRevenue' => $sessionRevenue,
            'ticketRevenue' => $ticketRevenue,
            'subscriptionRevenue' => $subscriptionRevenue,
            'totalRevenue' => $totalRevenue,
            'stationRevenue' => $stationRevenue,
            'shifts' => $shifts,
        ]);
    }
}
