<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientAssignment;

class TherapistController extends Controller
{
    public function index()
    {
        $assignments = ClientAssignment::where('therapist_id', auth()->id())
            ->whereDate('appointment_time', today())
            ->with(['service', 'client'])
            ->orderBy('appointment_time')
            ->get();

        $stats = [
            'total_clients' => $assignments->where('status', 'completed')->count(),
            'total_revenue' => $assignments->where('status', 'completed')->sum('final_cost'),
            'pending' => $assignments->where('status', 'pending')->count()
        ];

        $services = \App\Models\Service::where('status', 'active')->get();

        return view('therapist.dashboard', compact('assignments', 'services', 'stats'));
    }

    public function updateAssignment(Request $request, ClientAssignment $assignment)
    {
        if ($assignment->therapist_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'service_id' => 'nullable|exists:services,id',
            'final_cost' => 'nullable|numeric|min:0'
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'completed') {
            if (isset($validated['service_id'])) {
                $updateData['service_id'] = $validated['service_id'];
            }
            if (isset($validated['final_cost'])) {
                $updateData['final_cost'] = $validated['final_cost'];
            }
        }

        $assignment->update($updateData);

        return redirect()->back()->with('success', 'Status updated.');
    }
}
