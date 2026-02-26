<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\StaffAssignment;
use App\Models\Service;
use App\Models\Station;
use App\Models\AttendanceLog;
use App\Models\ShiftReport;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\TicketItem;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ClientAssignment;
use App\Models\User;

class ReceptionistController extends Controller
{
    public function index()
    {
        $activities = AttendanceLog::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->with('service')
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'time' => $log->created_at->format('h:i A'),
                    'customer' => $log->user_count > 1 ? $log->user_count . ' Guests' : 'Guest',
                    'service' => $log->service->name,
                    'revenue' => 'RWF ' . number_format($log->amount),
                    'status' => $log->status === 'draft' ? 'Pending' : 'Submitted'
                ];
            });

        return view('receptionist.dashboard', [
            'assignment' => $this->getEffectiveAssignment(),
            'activities' => $activities
        ]);
    }

    public function showEntry()
    {
        $activeAssignment = $this->getEffectiveAssignment();
        $stationName = $activeAssignment ? strtolower($activeAssignment->station->name) : '';
        $isGym = str_contains($stationName, 'gym');
        $isSaunaOrMassage = str_contains($stationName, 'sauna') || str_contains($stationName, 'massage');

        $gymService = $isGym ? Service::where('name', 'like', '%Gym%')->where('status', 'active')->first() : null;
        $saunaService = $isSaunaOrMassage ? Service::where('name', 'like', '%Sauna Only%')->where('status', 'active')->first() : null;
        $massageService = $isSaunaOrMassage ? Service::where('name', 'like', '%Massage%')->whereNotNull('id')->where('status', 'active')->get()->filter(fn($s) => stripos($s->name, 'sauna') === false)->first() : null;
        $saunaMassageService = $isSaunaOrMassage ? Service::where('status', 'active')->get()->filter(fn($s) => stripos($s->name, 'sauna') !== false && stripos($s->name, 'massage') !== false)->first() : null;

        // Build filtered services list (exclude gym services for non-gym stations)
        $services = Service::where('status', 'active')
            ->when(!$isGym && !$isSaunaOrMassage, fn($q) => $q->where('name', 'not like', '%Gym%')
                ->where('name', 'not like', '%Personal Training%')
                ->where('name', 'not like', '%Day Pass%')
                ->where('name', 'not like', '%Monthly Membership%'))
            ->get();

        return view('receptionist.entry', [
            'services' => $services,
            'activeAssignment' => $activeAssignment,
            'isGym' => $isGym,
            'isSaunaOrMassage' => $isSaunaOrMassage,
            'gymService' => $gymService,
            'saunaService' => $saunaService,
            'massageService' => $massageService,
            'saunaMassageService' => $saunaMassageService,
            'stations' => $activeAssignment ? [$activeAssignment->station] : [],
            'recentEntries' => AttendanceLog::where('user_id', auth()->id())
                ->whereDate('created_at', today())
                ->with(['service', 'station'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $activeAssignment = $this->getEffectiveAssignment();

        if (!$activeAssignment) {
            return redirect()->back()->with('error', 'You do not have an active assignment at this time.');
        }

        $stationName = strtolower($activeAssignment->station->name);
        $isGym = str_contains($stationName, 'gym');
        $isSaunaOrMassage = str_contains($stationName, 'sauna') || str_contains($stationName, 'massage');

        if ($isGym) {
            $gymService = Service::where('name', 'like', '%Gym%')->first();
            $request->merge(['service_id' => $gymService?->id]);
        }

        if ($isSaunaOrMassage) {
            // Service is chosen by the user from sauna/massage/combo options
            // The service_id is posted from the hidden input in the form — no override needed
            // Just ensure station doesn't have Gym service forced
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'station_id' => 'required|exists:stations,id|in:' . $activeAssignment->station_id,
            'user_count' => 'required|integer|min:1|max:100',
            'payment_method' => 'required|in:Cash,Mobile,Signature,Ticket,Subscription',
            'amount' => 'required_if:payment_method,Cash,Mobile|nullable|numeric|min:0',
            'subscription_id' => 'required_if:payment_method,Subscription|nullable|exists:subscriptions,id',
            'ticket_item_id' => 'required_if:payment_method,Ticket|nullable|exists:ticket_items,id',
        ], [
            'station_id.in' => 'You can only record entries for your assigned station: ' . $activeAssignment->station->name
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'draft';

        // Set unit price and calculate total amount
        $validated['unit_price'] = $request->amount ?? 0;
        $validated['amount'] = $validated['unit_price'] * $validated['user_count'];

        // Handle Ticket Usage
        if ($validated['payment_method'] === 'Ticket' && $validated['ticket_item_id']) {
            $item = TicketItem::find($validated['ticket_item_id']);
            if ($item) {
                $item->update(['is_used' => true]);

                // Check if all items in this ticket are used
                $allUsed = TicketItem::where('ticket_id', $item->ticket_id)
                    ->where('is_used', false)
                    ->count() === 0;

                if ($allUsed) {
                    Ticket::where('id', $item->ticket_id)->update(['status' => 'used']);
                }
            }
        }

        AttendanceLog::create($validated);

        return redirect()->back()->with('success', 'Entry recorded successfully.');
    }

    public function verifyCode(Request $request)
    {
        $code = $request->code;
        $type = $request->type; // 'subscription' or 'ticket'

        if ($type === 'subscription') {
            // In this system, subscription ID is used as the "code" for now or we could use phone
            $sub = Subscription::with('service')
                ->where(function ($q) use ($code) {
                    $q->where('id', $code)
                        ->orWhere('guest_phone', $code);
                })
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($sub) {
                return response()->json([
                    'success' => true,
                    'type' => 'subscription',
                    'id' => $sub->id,
                    'name' => $sub->guest_name,
                    'service' => $sub->service->name,
                    'service_id' => $sub->service_id,
                    'price' => $sub->price,
                    'expires' => $sub->end_date->format('M d, Y')
                ]);
            }
        } else {
            $item = TicketItem::with('ticket')
                ->where('code', $code)
                ->where('is_used', false)
                ->first();

            if ($item) {
                return response()->json([
                    'success' => true,
                    'type' => 'ticket',
                    'id' => $item->id,
                    'name' => $item->ticket->guest_name,
                    'price' => $item->ticket->price_per_ticket,
                    'service' => 'Prepaid Ticket',
                    // Assuming tickets are for a specific service or broad. 
                    // If tickets don't have service_id linked directly in model, we use a default or first
                    'service_id' => Service::where('name', 'like', '%Gym%')->first()?->id
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
    }

    public function history(Request $request)
    {
        $query = ShiftReport::where('user_id', auth()->id())
            ->with(['station', 'attendanceLogs.service'])
            ->orderBy('created_at', 'desc');

        // Optional filtering by month
        if ($request->month) {
            $query->whereMonth('created_at', substr($request->month, 5, 2))
                ->whereYear('created_at', substr($request->month, 0, 4));
        }

        return view('receptionist.history', [
            'reports' => $query->paginate(15),
            'filters' => $request->all()
        ]);
    }

    public function summary()
    {
        $logs = AttendanceLog::where('user_id', auth()->id())
            ->where('status', 'draft')
            ->with(['service', 'station', 'subscription', 'ticketItem.ticket'])
            ->get();

        return view('receptionist.summary', [
            'logs' => $logs,
            'summary' => [
                'total_cash' => $logs->where('payment_method', 'Cash')->sum('amount'),
                'total_momo' => $logs->where('payment_method', 'Mobile')->sum('amount'),
                'institution_users' => $logs->whereIn('payment_method', ['Signature', 'Subscription'])->sum('user_count'),
                'ticket_users' => $logs->where('payment_method', 'Ticket')->sum('user_count'),
                'total_users' => $logs->sum('user_count'),
                'total_revenue' => $logs->whereIn('payment_method', ['Cash', 'Mobile'])->sum('amount'),
                'gym_count' => $logs->filter(fn($l) => str_contains(strtolower($l->service->name), 'gym'))->sum('user_count'),
                'sauna_count' => $logs->filter(fn($l) => str_contains(strtolower($l->service->name), 'sauna') && !str_contains(strtolower($l->service->name), 'massage'))->sum('user_count'),
                'massage_count' => $logs->filter(fn($l) => str_contains(strtolower($l->service->name), 'massage') && !str_contains(strtolower($l->service->name), 'sauna'))->sum('user_count'),
                'combo_count' => $logs->filter(fn($l) => str_contains(strtolower($l->service->name), 'sauna') && str_contains(strtolower($l->service->name), 'massage'))->sum('user_count'),
            ]
        ]);
    }

    public function finalize(Request $request)
    {
        $logs = AttendanceLog::where('user_id', auth()->id())
            ->where('status', 'draft')
            ->get();

        if ($logs->isEmpty()) {
            return redirect()->back()->with('error', 'No pending logs to finalize.');
        }

        // Integrity check: ensure all logs are for the same station
        if ($logs->pluck('station_id')->unique()->count() > 1) {
            return redirect()->back()->with('error', 'Multiple stations detected in draft. Please contact admin to resolve log inconsistencies.');
        }

        DB::transaction(function () use ($logs) {
            $report = ShiftReport::create([
                'user_id' => auth()->id(),
                'station_id' => $logs->first()->station_id,
                'start_time' => $logs->min('created_at'),
                'end_time' => now(),
                'total_cash' => $logs->where('payment_method', 'Cash')->sum('amount'),
                'total_momo' => $logs->where('payment_method', 'Mobile')->sum('amount'),
                'total_revenue' => $logs->sum('amount'),
                'total_tickets' => $logs->where('payment_method', 'Ticket')->sum('user_count'),
                'total_subscriptions' => $logs->whereIn('payment_method', ['Signature', 'Subscription'])->sum('user_count'),
                'status' => 'submitted',
            ]);

            AttendanceLog::whereIn('id', $logs->pluck('id'))
                ->update([
                    'status' => 'aggregated',
                    'shift_report_id' => $report->id
                ]);
        });

        return redirect()->route('dashboard')->with('success', 'Shift finalized and submitted for review.');
    }

    public function reopen(Request $request, ShiftReport $report)
    {
        if ($report->user_id !== auth()->id()) {
            abort(403);
        }

        if ($report->status !== 'rejected') {
            return redirect()->back()->with('error', 'Only rejected reports can be reopened.');
        }

        DB::transaction(function () use ($report) {
            AttendanceLog::where('shift_report_id', $report->id)
                ->update([
                    'status' => 'draft',
                    'shift_report_id' => null
                ]);

            $report->delete();
        });

        return redirect()->route('receptionist.summary')
            ->with('success', 'Report reopened. Logs are back in your summary for editing.')
            ->with('rejection_reason', $report->rejection_reason);
    }

    public function destroyLog(Request $request, AttendanceLog $log)
    {
        if ($log->user_id !== auth()->id()) {
            abort(403);
        }

        if ($log->status !== 'draft') {
            return redirect()->back()->with('error', 'Cannot delete submitted logs.');
        }

        // Revert ticket usage if applicable
        if ($log->payment_method === 'Ticket' && $log->ticket_item_id) {
            $item = TicketItem::find($log->ticket_item_id);
            if ($item) {
                $item->update(['is_used' => false]);
                Ticket::where('id', $item->ticket_id)->update(['status' => 'paid']); // Revert to paid/active
            }
        }

        $log->delete();

        return redirect()->back()->with('success', 'Entry deleted.');
    }

    public function assignments()
    {
        $assignments = ClientAssignment::whereDate('appointment_time', today())
            ->with(['client', 'therapist', 'service'])
            ->orderBy('appointment_time')
            ->get();

        return view('receptionist.assignments', compact('assignments'));
    }

    public function showAssign()
    {
        $therapists = User::where('role', 'therapist')->where('status', 'active')->get();
        $services = Service::where('status', 'active')->get();

        return view('receptionist.assign', compact('therapists', 'services'));
    }

    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'therapist_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // Combine today's date with the time
        $appointmentTime = now()->format('Y-m-d') . ' ' . $validated['appointment_time'];

        ClientAssignment::create([
            'client_name' => $validated['client_name'],
            'therapist_id' => $validated['therapist_id'],
            'service_id' => $validated['service_id'],
            'appointment_time' => $appointmentTime,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('receptionist.assignments')->with('success', 'Client assigned successfully.');
    }

    public function editLog(AttendanceLog $log)
    {
        if ($log->user_id !== auth()->id()) {
            abort(403);
        }

        if ($log->status !== 'draft') {
            return redirect()->back()->with('error', 'Cannot edit submitted logs.');
        }

        $activeAssignment = $this->getEffectiveAssignment();
        $stationName = $activeAssignment ? strtolower($activeAssignment->station->name) : '';
        $isGym = str_contains($stationName, 'gym');
        $isSaunaOrMassage = str_contains($stationName, 'sauna') || str_contains($stationName, 'massage');
        $gymService = $isGym ? Service::where('name', 'like', '%Gym%')->where('status', 'active')->first() : null;
        $saunaService = $isSaunaOrMassage ? Service::where('name', 'like', '%Sauna Only%')->where('status', 'active')->first() : null;
        $massageService = $isSaunaOrMassage ? Service::where('status', 'active')->get()->filter(fn($s) => stripos($s->name, 'massage') !== false && stripos($s->name, 'sauna') === false)->first() : null;
        $saunaMassageService = $isSaunaOrMassage ? Service::where('status', 'active')->get()->filter(fn($s) => stripos($s->name, 'sauna') !== false && stripos($s->name, 'massage') !== false)->first() : null;

        return view('receptionist.edit-log', [
            'log' => $log,
            'services' => Service::where('status', 'active')->get(),
            'activeAssignment' => $activeAssignment,
            'isGym' => $isGym,
            'isSaunaOrMassage' => $isSaunaOrMassage,
            'gymService' => $gymService,
            'saunaService' => $saunaService,
            'massageService' => $massageService,
            'saunaMassageService' => $saunaMassageService,
        ]);
    }

    public function updateLog(Request $request, AttendanceLog $log)
    {
        if ($log->user_id !== auth()->id()) {
            abort(403);
        }

        if ($log->status !== 'draft') {
            return redirect()->back()->with('error', 'Cannot edit submitted logs.');
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'user_count' => 'required|integer|min:1|max:100',
            'payment_method' => 'required|in:Cash,Mobile,Signature,Ticket,Subscription',
            'amount' => 'required_if:payment_method,Cash,Mobile|nullable|numeric|min:0',
        ]);

        // Recalculate amounts
        $validated['unit_price'] = $request->amount ?? 0;
        $validated['amount'] = $validated['unit_price'] * $validated['user_count'];

        $log->update($validated);

        return redirect()->route('receptionist.summary')->with('success', 'Entry updated successfully.');
    }

    /**
     * Get the effective assignment for the current user.
     * Includes a 60-minute grace period before and after the shift.
     * If no active shift is found, it returns any assignment for the current day.
     */
    private function getEffectiveAssignment()
    {
        $today = now()->format('Y-m-d');
        $currentDay = strtoupper(now()->format('D'));
        $currentTime = now()->format('H:i');
        $graceBefore = now()->subMinutes(60)->format('H:i');
        $graceAfter = now()->addMinutes(60)->format('H:i');

        // 1. Try to find an exactly active assignment (with grace period)
        // Check for specific date first
        $active = StaffAssignment::where('user_id', auth()->id())
            ->where(function ($q) use ($today, $currentDay) {
                $q->where('assignment_date', $today)
                    ->orWhere(function ($sub) use ($currentDay) {
                        $sub->whereNull('assignment_date')->where('day_of_week', $currentDay);
                    });
            })
            ->where('start_time', '<=', $graceAfter)
            ->where('end_time', '>=', $graceBefore)
            ->with('station')
            ->orderByRaw('assignment_date DESC') // Specific date takes priority over recurring
            ->first();

        if ($active) {
            return $active;
        }

        // 2. If no "active" assignment, return ANY assignment for today
        return StaffAssignment::where('user_id', auth()->id())
            ->where(function ($q) use ($today, $currentDay) {
                $q->where('assignment_date', $today)
                    ->orWhere(function ($sub) use ($currentDay) {
                        $sub->whereNull('assignment_date')->where('day_of_week', $currentDay);
                    });
            })
            ->with('station')
            ->orderByRaw('assignment_date DESC')
            ->first();
    }
}