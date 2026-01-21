<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'items'])->withCount([
            'items as used_count' => function ($q) {
                $q->where('is_used', true);
            }
        ])->latest();

        // Get counts for tabs
        $counts = [
            'all' => Ticket::count(),
            'paid' => Ticket::where('status', 'paid')->count(),
            'used' => Ticket::where('status', 'used')->count(),
            'cancelled' => Ticket::where('status', 'cancelled')->count(),
        ];

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tickets = $query->get();
        $users = User::all(); // For selecting registered clients

        $viewFolder = auth()->user()->role === 'admin' ? 'admin' : 'manager';
        return view("{$viewFolder}.tickets.index", compact('tickets', 'users', 'counts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:1',
            'price_per_ticket' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,momo,card',
        ]);

        $data = [
            'type' => $validated['quantity'] > 1 ? 'bundle' : 'single',
            'quantity' => $validated['quantity'],
            'price_per_ticket' => $validated['price_per_ticket'],
            'total_amount' => $validated['quantity'] * $validated['price_per_ticket'],
            'payment_method' => $validated['payment_method'],
            'status' => 'paid',
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'user_id' => null, // Explicitly null as we are just recording names
        ];

        $ticket = Ticket::create($data);

        // Generate individual tickets
        for ($i = 0; $i < $ticket->quantity; $i++) {
            \App\Models\TicketItem::create([
                'ticket_id' => $ticket->id,
                'code' => strtoupper(\Illuminate\Support\Str::random(10)),
                'is_used' => false,
            ]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ticket Sale Recorded',
            'resource_type' => 'Ticket',
            'resource_id' => $ticket->id,
            'ip_address' => $request->ip(),
            'details' => ['client' => $ticket->guest_name, 'amount' => $ticket->total_amount]
        ]);

        $prefix = auth()->user()->role === 'admin' ? 'admin' : 'manager';

        // If it's a bundle, redirect to export, otherwise individual print
        if ($ticket->quantity > 1) {
            return redirect()->route("{$prefix}.tickets.export", $ticket)->with('success', 'Ticket bundle recorded. Ready for export.');
        }

        return redirect()->route("{$prefix}.tickets.print", $ticket)->with('success', 'Ticket sale recorded successfully.');
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'quantity' => 'required|integer|min:1',
            'price_per_ticket' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,momo,card',
            'status' => 'required|in:paid,used,cancelled',
        ]);

        $validated['total_amount'] = $validated['quantity'] * $validated['price_per_ticket'];

        $ticket->update($validated);

        // Sync TicketItem statuses
        if ($ticket->status === 'used') {
            $ticket->items()->update(['is_used' => true]);
        } elseif ($ticket->status === 'paid') {
            $ticket->items()->update(['is_used' => false]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ticket Updated',
            'resource_type' => 'Ticket',
            'resource_id' => $ticket->id,
            'ip_address' => $request->ip(),
            'details' => ['status' => $ticket->status]
        ]);

        return redirect()->back()->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ticket Deleted',
            'resource_type' => 'Ticket',
            'resource_id' => $ticket->id,
            'ip_address' => request()->ip(),
            'details' => ['client' => $ticket->guest_name]
        ]);

        $ticket->delete();
        return redirect()->back()->with('success', 'Ticket deleted.');
    }

    public function print(Ticket $ticket)
    {
        $viewFolder = auth()->user()->role === 'admin' ? 'admin' : 'manager';
        return view("{$viewFolder}.tickets.print", compact('ticket'));
    }

    public function export(Ticket $ticket)
    {
        // Backfill ticket items if they don't exist (for pre-existing sales)
        if ($ticket->items()->count() < $ticket->quantity) {
            $missing = $ticket->quantity - $ticket->items()->count();
            for ($i = 0; $i < $missing; $i++) {
                \App\Models\TicketItem::create([
                    'ticket_id' => $ticket->id,
                    'code' => strtoupper(\Illuminate\Support\Str::random(10)),
                    'is_used' => false,
                ]);
            }
        }

        $ticket->load('items');
        $viewFolder = auth()->user()->role === 'admin' ? 'admin' : 'manager';
        return view("{$viewFolder}.tickets.export", compact('ticket'));
    }
    public function items(Request $request)
    {
        $query = TicketItem::with(['ticket.user', 'attendanceLogs.receptionist', 'attendanceLogs.station'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereHas('ticket', function ($q2) use ($search) {
                        $q2->where('guest_name', 'like', "%{$search}%")
                            ->orWhere('guest_phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $isUsed = $request->status === 'used';
            $query->where('is_used', $isUsed);
        }

        $items = $query->paginate(20)->withQueryString();

        $viewFolder = auth()->user()->role === 'admin' ? 'admin' : 'manager';
        return view("{$viewFolder}.tickets.items", compact('items'));
    }
}
