<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TherapistReportController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/offline', function () {
    return file_get_contents(public_path('offline.html'));
});

Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    if ($role === 'admin') {
        return (new AdminController)->index();
    } elseif ($role === 'manager') {
        return (new ManagerController)->index();
    } elseif ($role === 'therapist') {
        return (new \App\Http\Controllers\TherapistController)->index();
    } else {
        return (new ReceptionistController)->index();
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/reports/export', [AdminController::class, 'exportReports'])->name('admin.reports.export');
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/admin/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
    Route::get('/admin/reports/therapists', [TherapistReportController::class, 'index'])->name('admin.reports.therapists');

    // User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::post('/admin/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');

    // Backward compatibility for old receptionist routes (proxied in controller)
    Route::get('/admin/receptionists', [AdminController::class, 'users'])->name('admin.receptionists');
    Route::post('/admin/receptionists', [AdminController::class, 'storeReceptionist'])->name('admin.receptionists.store');
    Route::put('/admin/receptionists/{user}', [AdminController::class, 'updateReceptionist'])->name('admin.receptionists.update');
    Route::delete('/admin/receptionists/{user}', [AdminController::class, 'destroyReceptionist'])->name('admin.receptionists.destroy');

    // Station Management
    Route::get('/admin/stations', [AdminController::class, 'stations'])->name('admin.stations');
    Route::post('/admin/stations', [AdminController::class, 'storeStation'])->name('admin.stations.store');
    Route::put('/admin/stations/{station}', [AdminController::class, 'updateStation'])->name('admin.stations.update');
    Route::delete('/admin/stations/{station}', [AdminController::class, 'destroyStation'])->name('admin.stations.destroy');
    Route::post('/admin/stations/{station}/toggle-status', [AdminController::class, 'toggleStationStatus'])->name('admin.stations.toggle-status');

    // Service Management
    Route::get('/admin/services', [ServiceController::class, 'index'])->name('admin.services');
    Route::post('/admin/services', [ServiceController::class, 'store'])->name('admin.services.store');
    Route::put('/admin/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy');
    Route::post('/admin/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('admin.services.toggle-status');

    // Subscription Plans Management
    Route::get('/admin/subscription-plans', [\App\Http\Controllers\SubscriptionPlanController::class, 'index'])->name('admin.subscription_plans.index');
    Route::post('/admin/subscription-plans', [\App\Http\Controllers\SubscriptionPlanController::class, 'store'])->name('admin.subscription_plans.store');
    Route::put('/admin/subscription-plans/{subscriptionPlan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'update'])->name('admin.subscription_plans.update');
    Route::delete('/admin/subscription-plans/{subscriptionPlan}', [\App\Http\Controllers\SubscriptionPlanController::class, 'destroy'])->name('admin.subscription_plans.destroy');
    Route::post('/admin/subscription-plans/{subscriptionPlan}/toggle-status', [\App\Http\Controllers\SubscriptionPlanController::class, 'toggleStatus'])->name('admin.subscription_plans.toggle-status');

    // Assignment Management (Shared logic)
    Route::get('/admin/assignments', [ManagerController::class, 'showAssign'])->name('admin.assignments');
    Route::post('/admin/assignments', [ManagerController::class, 'assignStaff'])->name('admin.assignments.store');
    Route::put('/admin/assignments/{assignment}', [ManagerController::class, 'updateAssignment'])->name('admin.assignments.update');
    Route::delete('/admin/assignments/{assignment}', [ManagerController::class, 'destroyAssignment'])->name('admin.assignments.destroy');

    // Review Reports (Admin View)
    Route::get('/admin/reviews', [ManagerController::class, 'review'])->name('admin.reviews');
    Route::post('/admin/reports/{report}/approve', [ManagerController::class, 'approve'])->name('admin.reports.approve');
    Route::post('/admin/reports/{report}/reject', [ManagerController::class, 'reject'])->name('admin.reports.reject');

    // Admin Operational Routes (Standalone Access)
    Route::get('/admin/subscriptions/export', [SubscriptionController::class, 'export'])->name('admin.subscriptions.export');
    Route::get('/admin/subscriptions/print', [SubscriptionController::class, 'print'])->name('admin.subscriptions.print');
    Route::resource('admin/subscriptions', SubscriptionController::class)->names('admin.subscriptions');

    Route::get('/admin/tickets', [TicketController::class, 'index'])->name('admin.tickets');
    Route::post('/admin/tickets', [TicketController::class, 'store'])->name('admin.tickets.store');
    Route::put('/admin/tickets/{ticket}', [TicketController::class, 'update'])->name('admin.tickets.update');
    Route::delete('/admin/tickets/{ticket}', [TicketController::class, 'destroy'])->name('admin.tickets.destroy');
    Route::get('/admin/tickets/{ticket}/print', [TicketController::class, 'print'])->name('admin.tickets.print');
    Route::get('/admin/tickets/{ticket}/export', [TicketController::class, 'export'])->name('admin.tickets.export');
    Route::get('/admin/ticket-items', [TicketController::class, 'items'])->name('admin.tickets.items');
});

Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerController::class, 'index'])->name('manager.dashboard');
    Route::get('/manager/assign', [ManagerController::class, 'showAssign'])->name('manager.assign');
    Route::post('/manager/assign-staff', [ManagerController::class, 'assignStaff'])->name('manager.assign.store');
    Route::put('/manager/assignments/{assignment}', [ManagerController::class, 'updateAssignment'])->name('manager.assign.update');
    Route::delete('/manager/assignments/{assignment}', [ManagerController::class, 'destroyAssignment'])->name('manager.assign.destroy');
    Route::delete('/manager/assignments/{assignment}', [ManagerController::class, 'destroyAssignment'])->name('manager.assign.destroy');
    Route::get('/manager/analytics', [AnalyticsController::class, 'index'])->name('manager.analytics');
    Route::get('/manager/reports/therapists', [TherapistReportController::class, 'index'])->name('manager.reports.therapists');
});

// Shared Admin & Manager Routes
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::get('/manager/review', [ManagerController::class, 'review'])->name('manager.review');
    Route::post('/manager/reports/{report}/approve', [ManagerController::class, 'approve'])->name('manager.reports.approve');
    Route::post('/manager/reports/{report}/reject', [ManagerController::class, 'reject'])->name('manager.reports.reject');

    // Subscription Management
    Route::get('/manager/subscriptions/export', [App\Http\Controllers\SubscriptionController::class, 'export'])->name('manager.subscriptions.export');
    Route::get('/manager/subscriptions/print', [App\Http\Controllers\SubscriptionController::class, 'print'])->name('manager.subscriptions.print');
    Route::resource('manager/subscriptions', App\Http\Controllers\SubscriptionController::class)->names('manager.subscriptions');

    // Tickets
    Route::get('/manager/tickets', [TicketController::class, 'index'])->name('manager.tickets');
    Route::post('/manager/tickets', [TicketController::class, 'store'])->name('manager.tickets.store');
    Route::put('/manager/tickets/{ticket}', [TicketController::class, 'update'])->name('manager.tickets.update');
    Route::delete('/manager/tickets/{ticket}', [TicketController::class, 'destroy'])->name('manager.tickets.destroy');
    Route::get('/manager/tickets/{ticket}/print', [TicketController::class, 'print'])->name('manager.tickets.print');
    Route::get('/manager/tickets/{ticket}/export', [TicketController::class, 'export'])->name('manager.tickets.export');
    Route::get('/manager/ticket-items', [TicketController::class, 'items'])->name('manager.tickets.items');
});

Route::middleware(['auth', 'role:receptionist'])->group(function () {
    Route::get('/reception/entry', [ReceptionistController::class, 'showEntry'])->name('receptionist.entry');
    Route::post('/reception/entry', [ReceptionistController::class, 'store'])->name('receptionist.store');
    Route::get('/reception/summary', [ReceptionistController::class, 'summary'])->name('receptionist.summary');
    Route::get('/reception/history', [ReceptionistController::class, 'history'])->name('receptionist.history');
    Route::get('/reception/verify', [ReceptionistController::class, 'verifyCode'])->name('receptionist.verify');
    Route::post('/reception/finalize', [ReceptionistController::class, 'finalize'])->name('receptionist.finalize');
    Route::post('/reception/reports/{report}/reopen', [ReceptionistController::class, 'reopen'])->name('receptionist.reports.reopen');
    Route::delete('/reception/logs/{log}', [ReceptionistController::class, 'destroyLog'])->name('receptionist.logs.destroy');
    Route::get('/reception/logs/{log}/edit', [ReceptionistController::class, 'editLog'])->name('receptionist.logs.edit');
    Route::put('/reception/logs/{log}', [ReceptionistController::class, 'updateLog'])->name('receptionist.logs.update');

    // Assignments
    Route::get('/reception/assignments', [ReceptionistController::class, 'assignments'])->name('receptionist.assignments');
    Route::get('/reception/assign', [ReceptionistController::class, 'showAssign'])->name('receptionist.assign');
    Route::post('/reception/assign', [ReceptionistController::class, 'storeAssignment'])->name('receptionist.assign.store');
});

Route::middleware(['auth', 'role:therapist'])->group(function () {
    Route::get('/therapist/dashboard', [App\Http\Controllers\TherapistController::class, 'index'])->name('therapist.dashboard');
    Route::put('/therapist/assignments/{assignment}', [App\Http\Controllers\TherapistController::class, 'updateAssignment'])->name('therapist.assignments.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shared Reporting
    Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
});

require __DIR__ . '/auth.php';
