<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ShiftReport;
use App\Models\Station;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index()
    {
        $today = now();
        $yesterday = now()->subDay();

        // 1. Revenue Today (Direct Collections + New Sales)
        $dateStr = $today->toDateString();

        // Finalized Shift Collections
        $srToday = ShiftReport::whereDate('created_at', $dateStr)
            ->whereIn('status', ['submitted', 'approved'])
            ->selectRaw('SUM(total_cash + total_momo) as total')
            ->value('total') ?? 0;

        // Real-time (Active/Draft) Shift Collections
        $draftToday = AttendanceLog::whereDate('created_at', $dateStr)
            ->where('status', 'draft')
            ->whereIn('payment_method', ['Cash', 'Mobile'])
            ->sum('amount');

        $tkToday = Ticket::whereDate('created_at', $dateStr)
            ->whereIn('status', ['paid', 'used'])
            ->sum('total_amount');

        $sbToday = Subscription::whereDate('created_at', $dateStr)
            ->sum('price');

        $revenueToday = $srToday + $draftToday + $tkToday + $sbToday;

        // 2. Revenue Yesterday
        $yestStr = $yesterday->toDateString();
        $srYesterday = ShiftReport::whereDate('created_at', $yestStr)
            ->whereIn('status', ['submitted', 'approved'])
            ->selectRaw('SUM(total_cash + total_momo) as total')
            ->value('total') ?? 0;

        $draftYesterday = AttendanceLog::whereDate('created_at', $yestStr)
            ->where('status', 'draft')
            ->whereIn('payment_method', ['Cash', 'Mobile'])
            ->sum('amount');

        $tkYesterday = Ticket::whereDate('created_at', $yestStr)
            ->whereIn('status', ['paid', 'used'])
            ->sum('total_amount');

        $sbYesterday = Subscription::whereDate('created_at', $yestStr)
            ->sum('price');

        $revenueYesterday = $srYesterday + $draftYesterday + $tkYesterday + $sbYesterday;

        $revenueTrend = $revenueYesterday > 0 ? (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100 : 0;

        $revenueHistory = collect(range(13, 0))->map(function ($days) {
            $date = now()->subDays($days);
            $dateStr = $date->toDateString();
            $shiftRev = ShiftReport::whereDate('created_at', $dateStr)->whereIn('status', ['submitted', 'approved'])->selectRaw('SUM(total_cash + total_momo) as total')->value('total') ?? 0;
            $draftRev = AttendanceLog::whereDate('created_at', $dateStr)->where('status', 'draft')->whereIn('payment_method', ['Cash', 'Mobile'])->sum('amount');
            $tickRev = Ticket::whereDate('created_at', $dateStr)->whereIn('status', ['paid', 'used'])->sum('total_amount');
            $subRev = Subscription::whereDate('created_at', $dateStr)->sum('price');

            return [
                'date' => $date->format('M d'),
                'revenue' => $shiftRev + $draftRev + $tickRev + $subRev
            ];
        });

        // Recent activity from AuditLog
        $staffActivity = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'name' => $log->user->name,
                    'action' => $log->action,
                    'time' => $log->created_at->diffForHumans(),
                    'status' => 'Completed' // Logs are historical, so completed
                ];
            });

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

        // 3. Lifetime Revenue
        $srTotal = ShiftReport::whereIn('status', ['submitted', 'approved'])->selectRaw('SUM(total_cash + total_momo) as total')->value('total') ?? 0;
        $tkTotal = Ticket::whereIn('status', ['paid', 'used'])->sum('total_amount');
        $sbTotal = Subscription::sum('price');
        $revenueTotal = $srTotal + $tkTotal + $sbTotal;

        // 5. Dynamic Service Usage
        $totalLogs = AttendanceLog::sum('user_count') ?: 1;
        $serviceUsage = AttendanceLog::select('service_id', DB::raw('SUM(user_count) as total'))
            ->groupBy('service_id')
            ->with('service')
            ->get()
            ->map(function ($log) use ($totalLogs) {
                return [
                    'name' => $log->service->name,
                    'value' => round(($log->total / $totalLogs) * 100),
                    'color' => '#' . substr(md5($log->service->name), 0, 6)
                ];
            });

        // 5. Consolidated System Activity
        $recentShifts = ShiftReport::with(['receptionist', 'station'])
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($report) {
                return [
                    'type' => 'shift_report',
                    'title' => 'Shift Approved',
                    'subtitle' => $report->receptionist->name . ' at ' . $report->station->name,
                    'amount' => 'RWF ' . number_format($report->total_cash + $report->total_momo),
                    'time' => $report->created_at,
                    'icon' => 'receipt_long',
                    'color' => 'blue'
                ];
            });

        $recentTickets = Ticket::whereIn('status', ['paid', 'used'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'type' => 'ticket',
                    'title' => 'Ticket Sale (' . $ticket->status . ')',
                    'subtitle' => ($ticket->guest_name ?? 'Guest') . ' (' . $ticket->quantity . ' items)',
                    'amount' => 'RWF ' . number_format($ticket->total_amount),
                    'time' => $ticket->created_at,
                    'icon' => 'confirmation_number',
                    'color' => 'purple'
                ];
            });

        $recentRedemptions = AttendanceLog::with(['receptionist', 'station', 'ticketItem.ticket'])
            ->whereNotNull('ticket_item_id')
            ->orderBy('created_at', 'desc')
            ->take(10)
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

        $recentSubscriptions = Subscription::with('service')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($sub) {
                return [
                    'type' => 'subscription',
                    'title' => 'New Subscription',
                    'subtitle' => ($sub->guest_name ?? 'Client') . ' - ' . $sub->service->name,
                    'amount' => 'RWF ' . number_format($sub->price),
                    'time' => $sub->created_at,
                    'icon' => 'card_membership',
                    'color' => 'green'
                ];
            });

        $unifiedActivity = $recentShifts->merge($recentTickets)->merge($recentSubscriptions)->merge($recentRedemptions)
            ->sortByDesc('time')
            ->take(10);

        return view('admin.dashboard', [
            'stats' => [
                'revenue' => [
                    'total' => $revenueTotal,
                    'today' => $revenueToday,
                    'trend' => round($revenueTrend, 1) . '%'
                ],
                'attendance' => [
                    'total' => AttendanceLog::sum('user_count'),
                    'today' => AttendanceLog::whereDate('created_at', $today)->sum('user_count'),
                ],
                'stations' => [
                    'active' => Station::where('status', 'active')->count(),
                    'total' => Station::count()
                ],
                'members' => [
                    'total' => User::count(),
                    'active' => User::whereHas('attendanceLogs', function ($q) use ($today) {
                        $q->whereDate('created_at', $today);
                    })->count()
                ],
                'pending_audits' => ShiftReport::where('status', 'submitted')->count()
            ],
            'pendingReportsList' => ShiftReport::with(['receptionist', 'station'])
                ->where('status', 'submitted')
                ->orderBy('created_at', 'desc')
                ->get(),
            'unifiedActivity' => $unifiedActivity,
            'revenueHistory' => $revenueHistory,
            'serviceUsage' => $serviceUsage,
            'staffActivity' => $staffActivity,
            'stationRevenue' => $stationRevenue
        ]);
    }

    public function stations()
    {
        return view('admin.stations.index', [
            'stations' => Station::all()
        ]);
    }

    public function storeStation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stations,name',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $station = Station::create($validated);

        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Station Created',
            'resource_type' => 'Station',
            'resource_id' => $station->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $station->name]
        ]);

        return redirect()->back()->with('success', 'Station created successfully');
    }

    public function updateStation(Request $request, Station $station)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stations,name,' . $station->id,
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $station->update($validated);

        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Station Updated',
            'resource_type' => 'Station',
            'resource_id' => $station->id,
            'ip_address' => $request->ip(),
            'details' => ['changes' => $station->getChanges()]
        ]);

        return redirect()->back()->with('success', 'Station updated successfully');
    }

    public function destroyStation(Request $request, Station $station)
    {
        // Check if station has usage logs or reports
        if ($station->attendanceLogs()->exists() || $station->shiftReports()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete station with existing records. Deactivate it instead.');
        }

        $stationId = $station->id;
        $stationName = $station->name;
        $station->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Station Deleted',
            'resource_type' => 'Station',
            'resource_id' => $stationId,
            'ip_address' => $request->ip(),
            'details' => ['name' => $stationName]
        ]);

        return redirect()->back()->with('success', 'Station deleted successfully');
    }

    public function toggleStationStatus(Request $request, Station $station)
    {
        $newStatus = $request->status === 'active' ? 'active' : 'inactive';
        $oldStatus = $station->status;
        $station->update(['status' => $newStatus]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Station Status Changed',
            'resource_type' => 'Station',
            'resource_id' => $station->id,
            'ip_address' => $request->ip(),
            'details' => ['old_status' => $oldStatus, 'new_status' => $newStatus]
        ]);

        return redirect()->back()->with('success', "Station marked as $newStatus.");
    }

    public function users()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('created_at', 'desc')->get()
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,manager,receptionist',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'status' => 'active'
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Created',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'ip_address' => $request->ip(),
            'details' => ['email' => $user->email, 'role' => $user->role]
        ]);

        return redirect()->back()->with('success', 'User created successfully');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,receptionist',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,suspended,inactive'
        ]);

        $user->update($validated);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Updated',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'ip_address' => $request->ip(),
            'details' => ['changes' => $user->getChanges()]
        ]);

        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function destroyUser(Request $request, User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $userId = $user->id;
        $userEmail = $user->email;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Deleted',
            'resource_type' => 'User',
            'resource_id' => $userId,
            'ip_address' => $request->ip(),
            'details' => ['email' => $userEmail]
        ]);

        return redirect()->back()->with('success', 'User deleted successfully');
    }

    public function toggleUserStatus(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot change your own status.');
        }

        $newStatus = $request->status;
        if (!in_array($newStatus, ['active', 'suspended', 'inactive'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $oldStatus = $user->status;
        $user->update(['status' => $newStatus]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Status Changed',
            'resource_type' => 'User',
            'resource_id' => $user->id,
            'ip_address' => $request->ip(),
            'details' => ['old_status' => $oldStatus, 'new_status' => $newStatus]
        ]);

        return redirect()->back()->with('success', "User marked as $newStatus.");
    }

    // Keep for backward compatibility if needed, but proxy to generic methods
    public function storeReceptionist(Request $request)
    {
        $request->merge(['role' => 'receptionist']);
        return $this->storeUser($request);
    }

    public function updateReceptionist(Request $request, User $user)
    {
        $request->merge(['role' => 'receptionist', 'status' => $user->status]);
        return $this->updateUser($request, $user);
    }

    public function destroyReceptionist(Request $request, User $user)
    {
        return $this->destroyUser($request, $user);
    }

    public function reports(Request $request)
    {
        $query = AttendanceLog::with(['service', 'station', 'user'])->orderBy('created_at', 'desc');

        // Apply Filters
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->station_id) {
            $query->where('station_id', $request->station_id);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $logs = $query->paginate(50);

        // Calculate Summary for the current filter
        $shiftRevenue = (clone $query)->whereIn('payment_method', ['Cash', 'Mobile'])->sum('amount');

        $ticketQuery = Ticket::whereIn('status', ['paid', 'used']);
        $subQuery = Subscription::query();

        if ($request->date_from) {
            $ticketQuery->whereDate('created_at', '>=', $request->date_from);
            $subQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $ticketQuery->whereDate('created_at', '<=', $request->date_to);
            $subQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $ticketRevenue = $ticketQuery->sum('total_amount');
        $subscriptionRevenue = $subQuery->sum('price');

        $summaryStats = [
            'shift_revenue' => $shiftRevenue,
            'ticket_revenue' => $ticketRevenue,
            'subscription_revenue' => $subscriptionRevenue,
            'total_revenue' => $shiftRevenue + $ticketRevenue + $subscriptionRevenue,
            'total_checkins' => (clone $query)->sum('user_count'),
            'momo_percent' => (clone $query)->where('payment_method', 'Mobile')->sum('amount') > 0
                ? round(((clone $query)->where('payment_method', 'Mobile')->sum('amount') / max(1, $shiftRevenue)) * 100)
                : 0,
        ];

        return view('admin.reports', [
            'logs' => $logs,
            'stats' => $summaryStats,
            'stations' => Station::all(),
            'filters' => $request->all()
        ]);
    }

    public function exportReports(Request $request)
    {
        $query = AttendanceLog::with(['service', 'station', 'user'])->orderBy('created_at', 'desc');

        // Apply Filters (Duplicate logic to match reports view)
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->station_id) {
            $query->where('station_id', $request->station_id);
        }
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $logs = $query->get();

        $filename = "reports_export_" . date('Y-m-d_H-i') . ".csv";

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Receptionist', 'Service', 'Price', 'Station', 'Payment Method', 'User Count', 'Total Amount']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->name,
                    $log->service->name,
                    $log->unit_price ?? $log->service->base_price,
                    $log->station->name,
                    $log->payment_method,
                    $log->user_count,
                    $log->amount
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function logs(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('action', 'like', "%{$request->search}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->event_type) {
            $query->where('action', $request->event_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return view('admin.logs', ['logs' => $logs]);
    }
}
