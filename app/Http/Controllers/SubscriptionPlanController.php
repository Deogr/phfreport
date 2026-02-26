<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = \App\Models\SubscriptionPlan::with('services')->latest()->get();
        $services = \App\Models\Service::where('status', 'active')->get();
        return view('admin.subscription_plans.index', compact('plans', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $plan = \App\Models\SubscriptionPlan::create($request->except('services'));

        if ($request->has('services')) {
            $plan->services()->attach($request->services);
        }

        return redirect()->back()->with('success', 'Subscription plan created successfully.');
    }

    public function update(Request $request, \App\Models\SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $subscriptionPlan->update($request->except('services'));

        if ($request->has('services')) {
            $subscriptionPlan->services()->sync($request->services);
        } else {
            $subscriptionPlan->services()->detach();
        }

        return redirect()->back()->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(\App\Models\SubscriptionPlan $subscriptionPlan)
    {
        if ($subscriptionPlan->subscriptions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete plan because it is currently assigned to subscriptions.');
        }

        $subscriptionPlan->delete();

        return redirect()->back()->with('success', 'Subscription plan deleted successfully.');
    }

    public function toggleStatus(\App\Models\SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->update([
            'status' => $subscriptionPlan->status === 'active' ? 'inactive' : 'active'
        ]);

        $status = $subscriptionPlan->status === 'active' ? 'enabled' : 'disabled';
        return redirect()->back()->with('success', "Subscription plan {$status} successfully.");
    }
}
