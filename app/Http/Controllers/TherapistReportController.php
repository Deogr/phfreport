<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClientAssignment;
use App\Models\User;

class TherapistReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientAssignment::with(['client', 'therapist', 'service'])
            ->where('status', 'completed')
            ->orderBy('appointment_time', 'desc');

        if ($request->date_from) {
            $query->whereDate('appointment_time', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('appointment_time', '<=', $request->date_to);
        }
        if ($request->therapist_id) {
            $query->where('therapist_id', $request->therapist_id);
        }

        $assignments = $query->paginate(20)->withQueryString();

        $stats = [
            'total_revenue' => (clone $query)->sum('final_cost'),
            'total_clients' => (clone $query)->count(),
        ];

        $therapists = User::where('role', 'therapist')->get();

        return view('admin.reports.therapist', compact('assignments', 'stats', 'therapists', 'request'));
    }
}
