@php
    $navClass = "flex items-center gap-3 px-3 py-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors";
    $activeClass = "flex items-center gap-3 px-3 py-2.5 rounded-md bg-primary text-white shadow-sm transition-colors group";
@endphp

<!-- Dashboard -->
<a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? $activeClass : $navClass }}">
    <span
        class="material-symbols-outlined text-[20px]">{{ request()->routeIs('dashboard') ? 'dashboard' : 'dashboard' }}</span>
    <span class="text-sm font-medium">Dashboard</span>
</a>

@if(auth()->user()->role === 'admin')
    <div class="mt-6 mb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
        System Management
    </div>
    <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">payments</span>
        <span class="text-sm font-medium">Financial Reports</span>
    </a>
    <a href="{{ route('reports.daily') }}" class="{{ request()->routeIs('reports.daily*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">account_balance</span>
        <span class="text-sm font-medium">Daily Revenue</span>
    </a>
    <a href="{{ route('admin.analytics') }}"
        class="{{ request()->routeIs('admin.analytics*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">analytics</span>
        <span class="text-sm font-medium">Insights & Analytics</span>
    </a>
    <a href="{{ route('admin.reviews') }}" class="{{ request()->routeIs('admin.reviews*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">rate_review</span>
        <span class="text-sm font-medium">Review Reports</span>
    </a>
    <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">history</span>
        <span class="text-sm font-medium">Audit Logs</span>
    </a>

    <div class="mt-6 mb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
        Administration
    </div>
    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
        <span class="text-sm font-medium">User Management</span>
    </a>
    <a href="{{ route('admin.stations') }}" class="{{ request()->routeIs('admin.stations*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">settings_input_component</span>
        <span class="text-sm font-medium">Stations</span>
    </a>
    <a href="{{ route('admin.services') }}" class="{{ request()->routeIs('admin.services*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">spa</span>
        <span class="text-sm font-medium">Services</span>
    </a>
    <a href="{{ route('admin.assignments') }}"
        class="{{ request()->routeIs('admin.assignments*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
        <span class="text-sm font-medium">Scheduling</span>
    </a>
    <a href="{{ route('admin.subscriptions.index') }}"
        class="{{ request()->routeIs('admin.subscriptions*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">card_membership</span>
        <span class="text-sm font-medium">Subscriptions</span>
    </a>
    <a href="{{ route('admin.tickets') }}" class="{{ request()->routeIs('admin.tickets*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
        <span class="text-sm font-medium">Tickets</span>
    </a>
@endif

@if(auth()->user()->role === 'manager')
    <div class="mt-6 mb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
        Operations
    </div>
    <a href="{{ route('manager.assign') }}" class="{{ request()->routeIs('manager.assign*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
        <span class="text-sm font-medium">Scheduling</span>
    </a>
    <a href="{{ route('manager.review') }}" class="{{ request()->routeIs('manager.review*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">rate_review</span>
        <span class="text-sm font-medium">Review Reports</span>
    </a>
    <a href="{{ route('manager.subscriptions.index') }}"
        class="{{ request()->routeIs('manager.subscriptions*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">card_membership</span>
        <span class="text-sm font-medium">Subscriptions</span>
    </a>
    <a href="{{ route('manager.tickets') }}"
        class="{{ request()->routeIs('manager.tickets*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
        <span class="text-sm font-medium">Tickets</span>
    </a>
    <a href="{{ route('manager.analytics') }}"
        class="{{ request()->routeIs('manager.analytics*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">insights</span>
        <span class="text-sm font-medium">Analytics</span>
    </a>
    <a href="{{ route('reports.daily') }}" class="{{ request()->routeIs('reports.daily*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">account_balance</span>
        <span class="text-sm font-medium">Daily Revenue</span>
    </a>
@endif

@if(auth()->user()->role === 'receptionist')
    <div class="mt-6 mb-2 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
        Daily Tasks
    </div>
    <a href="{{ route('receptionist.entry') }}"
        class="{{ request()->routeIs('receptionist.entry*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">edit_square</span>
        <span class="text-sm font-medium">Daily Entry</span>
    </a>
    <a href="{{ route('receptionist.summary') }}"
        class="{{ request()->routeIs('receptionist.summary*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">summarize</span>
        <span class="text-sm font-medium">Shift Summary</span>
    </a>
    <a href="{{ route('receptionist.history') }}"
        class="{{ request()->routeIs('receptionist.history*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">history</span>
        <span class="text-sm font-medium">Report History</span>
    </a>
    <a href="{{ route('reports.daily') }}" class="{{ request()->routeIs('reports.daily*') ? $activeClass : $navClass }}">
        <span class="material-symbols-outlined text-[20px]">account_balance</span>
        <span class="text-sm font-medium">My Daily Revenue</span>
    </a>
@endif