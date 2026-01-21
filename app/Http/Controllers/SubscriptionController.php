<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Service;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'service', 'attendanceLogs'])->latest()->get();
        $totalSubscriptions = $subscriptions->count();
        $activeSubscriptions = $subscriptions->where('status', 'active')->count();
        $expiredSubscriptions = $subscriptions->where('status', 'expired')->count();

        $users = User::all(); // For creating new subscription
        $services = Service::where('status', 'active')->get();

        $viewFolder = auth()->user()->role === 'admin' ? 'admin' : 'manager';
        return view("{$viewFolder}.subscriptions.index", compact('subscriptions', 'users', 'services', 'totalSubscriptions', 'activeSubscriptions', 'expiredSubscriptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = null; // Ensuring user_id is null for manual entry

        $subscription = Subscription::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Subscription Created',
            'resource_type' => 'Subscription',
            'resource_id' => $subscription->id,
            'ip_address' => $request->ip(),
            'details' => ['client' => $subscription->guest_name, 'amount' => $subscription->price]
        ]);

        return redirect()->back()->with('success', 'Subscription created successfully.');
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,cancelled',
        ]);

        $subscription->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Subscription Updated',
            'resource_type' => 'Subscription',
            'resource_id' => $subscription->id,
            'ip_address' => $request->ip(),
            'details' => ['status' => $subscription->status]
        ]);

        return redirect()->back()->with('success', 'Subscription status updated.');
    }

    public function destroy(Subscription $subscription)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Subscription Deleted',
            'resource_type' => 'Subscription',
            'resource_id' => $subscription->id,
            'ip_address' => request()->ip(),
            'details' => ['client' => $subscription->guest_name]
        ]);

        $subscription->delete();
        return redirect()->back()->with('success', 'Subscription deleted.');
    }

    public function export()
    {
        $subscriptions = Subscription::with(['user', 'service'])->latest()->get();
        $filename = "subscriptions_export_" . date('Y-m-d_H-i-s') . ".csv";

        $handle = fopen('php://output', 'w');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($handle, $subscriptions) {
            fputcsv($handle, ['ID', 'Client Name', 'Client Phone', 'Service', 'Start Date', 'End Date', 'Price', 'Status']);

            foreach ($subscriptions as $sub) {
                /** @var \App\Models\Subscription $sub */
                $name = $sub->user ? $sub->user->name : $sub->guest_name;
                fputcsv($handle, [
                    $sub->id,
                    $name,
                    $sub->guest_phone ?? 'N/A',
                    $sub->service->name,
                    $sub->start_date->format('Y-m-d'),
                    $sub->end_date->format('Y-m-d'),
                    $sub->price,
                    ucfirst($sub->status)
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    public function print()
    {
        $subscriptions = Subscription::with(['user', 'service'])->latest()->get();
        $viewFolder = auth()->user()->role === 'admin' ? 'admin' : 'manager';
        return view("{$viewFolder}.subscriptions.print", compact('subscriptions'));
    }
}
