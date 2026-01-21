<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.services.index', [
            'services' => Service::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $service = Service::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Service Created',
            'resource_type' => 'Service',
            'resource_id' => $service->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $service->name, 'price' => $service->price]
        ]);

        return redirect()->back()->with('success', 'Service created successfully');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $service->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Service Updated',
            'resource_type' => 'Service',
            'resource_id' => $service->id,
            'ip_address' => $request->ip(),
            'details' => ['changes' => $service->getChanges()]
        ]);

        return redirect()->back()->with('success', 'Service updated successfully');
    }

    public function toggleStatus(Request $request, Service $service)
    {
        $newStatus = $request->status === 'active' ? 'active' : 'inactive';
        $oldStatus = $service->status;
        $service->update(['status' => $newStatus]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Service Status Changed',
            'resource_type' => 'Service',
            'resource_id' => $service->id,
            'ip_address' => $request->ip(),
            'details' => ['old_status' => $oldStatus, 'new_status' => $newStatus]
        ]);

        return redirect()->back()->with('success', "Service marked as $newStatus.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Service $service)
    {
        // Check if service has usage logs
        if ($service->attendanceLogs()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete service with existing attendance records. Deactivate it instead.');
        }

        $service->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Service Deleted',
            'resource_type' => 'Service',
            'resource_id' => $service->id,
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Service deleted successfully');
    }
}
