<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ShiftReport;
use App\Models\StaffAssignment;
use App\Models\User;
use App\Models\Station;
use App\Models\AttendanceLog;
use App\Models\Subscription;
use App\Models\Ticket;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function index()
    {
        $today = now();

        // 1. Shift Report Revenue (Receptionists)
        $shiftRevenue = ShiftReport::whereDate('created_at', $today)->whereIn('status', ['submitted', 'approved'])->selectRaw('SUM(total_cash + total_momo) as total')->value('total') ?? 0;

        // 2. Ticket Revenue (Managers)
        $ticketRevenue = Ticket::whereDate('created_at', $today)
            ->whereIn('status', ['paid', 'used'])
            ->sum('total_amount');

        // 3. Subscription Revenue (Managers)
        $subscriptionRevenue = Subscription::whereDate('created_at', $today)
            ->sum('price');

        $totalRevenueToday = $shiftRevenue + $ticketRevenue + $subscriptionRevenue;

        // Expiring Subscriptions (Next 7 days)
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->count();

        $pendingReports = ShiftReport::where('status', 'submitted')->count();

        // Fetch Activities for Unified Stream
        $recentShifts = ShiftReport::with(['receptionist', 'station'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'shift_report',
                    'title' => 'Shift ' . ucfirst($report->status),
                    'subtitle' => $report->receptionist->name . ' at ' . $report->station->name,
                    'amount' => 'RWF ' . number_format($report->total_revenue),
                    'time' => $report->created_at,
                    'icon' => 'receipt_long',
                    'color' => 'blue'
                ];
            });

        $recentTickets = Ticket::whereIn('status', ['paid', 'used'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'type' => 'ticket',
                    'title' => 'Ticket Issued (' . $ticket->status . ')',
                    'subtitle' => $ticket->guest_name ?? 'Guest',
                    'amount' => 'RWF ' . number_format($ticket->total_amount),
                    'time' => $ticket->created_at,
                    'icon' => 'confirmation_number',
                    'color' => 'purple'
                ];
            });

        $recentRedemptions = AttendanceLog::with(['receptionist', 'station', 'ticketItem.ticket'])
            ->whereNotNull('ticket_item_id')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'redemption',
                    'title' => 'Ticket Used',
                    'subtitle' => ($log->ticketItem->ticket->guest_name ?? 'Guest') . ' - Code: ' . $log->ticketItem->code . ' at ' . $log->station->name,
                    'amount' => 'Redeemed',
                    'time' => $log->created_at,
                    'icon' => 'check_circle',
                    'color' => 'orange'
                ];
            });

        $recentSubs = Subscription::with('service')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'subscription',
                    'title' => 'Member Joined',
                    'subtitle' => ($sub->guest_name ?? 'Client') . ' (' . $sub->service->name . ')',
                    'amount' => 'RWF ' . number_format($sub->price),
                    'time' => $sub->created_at,
                    'icon' => 'card_membership',
                    'color' => 'green'
                ];
            });

        $unifiedActivity = $recentShifts->merge($recentTickets)->merge($recentSubs)->merge($recentRedemptions)
            ->sortByDesc('time')
            ->take(5);

        // Revenue per Station (Shifts)
        $stationRevenue = ShiftReport::whereDate('created_at', $today)
            ->where('status', 'approved')
            ->with('station')
            ->get()
            ->groupBy('station_id')
            ->map(function ($shifts) {
                return [
                    'name' => $shifts->first()->station->name ?? 'Unknown',
                    'revenue' => $shifts->sum('total_revenue'),
                    'count' => $shifts->count()
                ];
            });

        return view('manager.dashboard', [
            'pendingReportsList' => ShiftReport::with(['receptionist', 'station'])
                ->where('status', 'submitted')
                ->orderBy('created_at', 'desc')
                ->get(),
            'unifiedActivity' => $unifiedActivity,
            'stationRevenue' => $stationRevenue,
            'stats' => [
                'pendingApprovals' => $pendingReports,
                'activeSubscriptions' => Subscription::where('status', 'active')->count(),
                'expiringSubscriptions' => $expiringSubscriptions,
                'ticketsSold' => Ticket::sum('quantity'),
                'criticalAlerts' => 0,
                'revenue' => [
                    'current' => $totalRevenueToday,
                    'target' => 5000000,
                    'trend' => '+12.5%' // You can make this dynamic later if needed
                ],
                'membership' => [
                    'active' => User::count(),
                    'checkins' => AttendanceLog::whereDate('created_at', now())->sum('user_count'),
                    'staffOnDuty' => User::where('role', 'receptionist')
                        ->whereHas('attendanceLogs', function ($q) {
                            $q->whereDate('created_at', now());
                        })->count()
                ]
            ]
        ]);
    }

    public function review()
    {
        $reports = ShiftReport::where('status', 'submitted')
            ->with(['receptionist', 'station', 'attendanceLogs.service'])
            ->orderBy('created_at', 'desc')
            ->get();

        $view = auth()->user()->role === 'admin' ? 'admin.reviews' : 'manager.review';
        return view($view, compact('reports'));
    }

    public function approve(Request $request, ShiftReport $report)
    {
        if ($report->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted reports can be approved.');
        }

        $report->update([
            'status' => 'approved',
            'manager_id' => auth()->id(),
            'approved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Report approved successfully.');
    }

    public function reject(Request $request, ShiftReport $report)
    {
        if ($report->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted reports can be rejected.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $report->update([
            'status' => 'rejected',
            'manager_id' => auth()->id(),
            'rejection_reason' => $validated['reason'] ?? null
        ]);

        return redirect()->back()->with('success', 'Report rejected.');
    }

    public function showAssign()
    {
        return view('manager.assign', [
            'staff' => User::where('role', 'receptionist')->where('status', 'active')->get(),
            'stations' => Station::where('status', 'active')->get(),
            'assignments' => StaffAssignment::with(['user', 'station'])->get(),
            'routeBase' => auth()->user()->role === 'admin' ? 'admin.assignments' : 'manager.assign'
        ]);
    }

    public function assignStaff(Request $request)
    {
        $validated = $request->validate([
            'staff' => 'required|exists:users,id',
            'station' => 'required|exists:stations,id',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'assignment_date' => 'nullable|date',
            'days' => 'nullable|array',
            'days.*' => 'in:MON,TUE,WED,THU,FRI,SAT,SUN'
        ]);

        if (empty($validated['assignment_date']) && empty($validated['days'])) {
            return redirect()->back()->with('error', 'Please specify a date or select recurring days.');
        }

        $items = [];
        if (!empty($validated['assignment_date'])) {
            $date = \Carbon\Carbon::parse($validated['assignment_date']);
            $items[] = [
                'date' => $date->format('Y-m-d'),
                'day' => strtoupper($date->format('D'))
            ];
        } else {
            foreach ($validated['days'] as $day) {
                $items[] = [
                    'date' => null,
                    'day' => $day
                ];
            }
        }

        foreach ($items as $item) {
            $conflict = StaffAssignment::where('user_id', $validated['staff'])
                ->where(function ($q) use ($item) {
                    if ($item['date']) {
                        $q->where('assignment_date', $item['date'])
                            ->orWhere(function ($sub) use ($item) {
                                $sub->whereNull('assignment_date')->where('day_of_week', $item['day']);
                            });
                    } else {
                        // For recurring, check against same day (both specific and recurring)
                        $q->where('day_of_week', $item['day']);
                    }
                })
                ->where(function ($q) use ($validated) {
                    $q->where(function ($q3) use ($validated) {
                        $q3->whereBetween('start_time', [$validated['startTime'], $validated['endTime']])
                            ->orWhereBetween('end_time', [$validated['startTime'], $validated['endTime']])
                            ->orWhere(function ($q2) use ($validated) {
                                $q2->where('start_time', '<=', $validated['startTime'])
                                    ->where('end_time', '>=', $validated['endTime']);
                            });
                    });
                })->exists();

            if ($conflict) {
                $msg = $item['date'] ? "Shift conflict detected for {$item['date']}." : "Shift conflict detected for {$item['day']}.";
                return redirect()->back()->with('error', "$msg Please choose different times.");
            }

            StaffAssignment::create([
                'user_id' => $validated['staff'],
                'station_id' => $validated['station'],
                'assignment_date' => $item['date'],
                'day_of_week' => $item['day'],
                'start_time' => $validated['startTime'],
                'end_time' => $validated['endTime']
            ]);
        }

        return redirect()->back()->with('success', 'Staff assigned successfully.');
    }

    public function updateAssignment(Request $request, StaffAssignment $assignment)
    {
        $validated = $request->validate([
            'station_id' => 'required|exists:stations,id',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'assignment_date' => 'nullable|date',
            'day_of_week' => 'nullable|in:MON,TUE,WED,THU,FRI,SAT,SUN'
        ]);

        $assignmentDate = $validated['assignment_date'] ?? null;
        $dayOfWeek = $validated['day_of_week'] ?? $assignment->day_of_week;

        if (!empty($assignmentDate)) {
            // If date is set, force day_of_week to match
            $dayOfWeek = strtoupper(\Carbon\Carbon::parse($assignmentDate)->format('D'));
        }

        if (empty($assignmentDate) && empty($dayOfWeek)) {
            return redirect()->back()->with('error', 'Either a specific date or a day of week must be provided.');
        }

        // Check for conflicts excluding the current assignment
        $conflict = StaffAssignment::where('user_id', $assignment->user_id)
            ->where('id', '!=', $assignment->id)
            ->where(function ($q) use ($assignmentDate, $dayOfWeek) {
                if ($assignmentDate) {
                    $q->where('assignment_date', $assignmentDate)
                        ->orWhere(function ($sub) use ($dayOfWeek) {
                            $sub->whereNull('assignment_date')->where('day_of_week', $dayOfWeek);
                        });
                } else {
                    $q->where('day_of_week', $dayOfWeek);
                }
            })
            ->where(function ($q) use ($validated) {
                $q->where(function ($q3) use ($validated) {
                    $q3->whereBetween('start_time', [$validated['startTime'], $validated['endTime']])
                        ->orWhereBetween('end_time', [$validated['startTime'], $validated['endTime']])
                        ->orWhere(function ($q2) use ($validated) {
                            $q2->where('start_time', '<=', $validated['startTime'])
                                ->where('end_time', '>=', $validated['endTime']);
                        });
                });
            })->exists();

        if ($conflict) {
            return redirect()->back()->with('error', "Shift conflict detected. Please choose different times.");
        }

        $assignment->update([
            'station_id' => $validated['station_id'],
            'assignment_date' => $assignmentDate,
            'day_of_week' => $dayOfWeek,
            'start_time' => $validated['startTime'],
            'end_time' => $validated['endTime']
        ]);

        return redirect()->back()->with('success', 'Assignment updated successfully.');
    }

    public function destroyAssignment(StaffAssignment $assignment)
    {
        $assignment->delete();
        return redirect()->back()->with('success', 'Assignment removed.');
    }
}
