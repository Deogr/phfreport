<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Subscription Management') }}
        </h2>
    </x-slot>

    <script>
        var managerSubscriptionPlans = {!! json_encode($plans) !!};
    </script>

    <div class="py-12" x-data="managerSubscriptionsApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Actions -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <!-- Stats Area -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full md:w-3/4 mb-4 md:mb-0">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total Subscriptions
                        </div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSubscriptions }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <div class="text-green-500 dark:text-green-400 text-sm font-medium uppercase">Active Now</div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">
                            {{ $activeSubscriptions }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <div class="text-red-500 dark:text-red-400 text-sm font-medium uppercase">Expired</div>
                        <div class="text-3xl font-bold text-red-600 dark:text-red-400 mt-2">{{ $expiredSubscriptions }}
                        </div>
                    </div>
                </div>

                <div class="flex space-x-2 md:ml-4">
                    <a href="{{ route('manager.subscriptions.export') }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded h-10 flex items-center">
                        CSV
                    </a>
                    <a href="{{ route('manager.subscriptions.print') }}" target="_blank"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded h-10 flex items-center">
                        Print
                    </a>
                    <button @click="showCreateModal = true"
                        class="bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded h-10">
                        Add New
                    </button>
                </div>
            </div>

            <!-- Subscriptions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4">Active & Recent Subscriptions</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Plan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Dates</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Days Left</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Usage</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Price</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($subscriptions as $sub)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $sub->user ? $sub->user->name : $sub->guest_name }}
                                            @if($sub->guest_phone)
                                                <span class="text-xs text-gray-400 block">{{ $sub->guest_phone }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sub->subscriptionPlan ? $sub->subscriptionPlan->name : ($sub->service ? $sub->service->name : 'Legacy') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $sub->start_date->format('M d, Y') }} -
                                            {{ $sub->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @php
                                                $daysLeft = now()->startOfDay()->diffInDays($sub->end_date, false);
                                            @endphp
                                            @if($sub->status === 'active')
                                                @if($daysLeft > 0)
                                                    <span class="text-green-600 dark:text-green-400">{{ $daysLeft }} Days</span>
                                                @elseif($daysLeft == 0)
                                                    <span class="text-orange-500 font-bold">Expires Today</span>
                                                @else
                                                    <span class="text-red-500">Overdue</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span
                                                class="text-gray-900 dark:text-white font-bold">{{ count($sub->attendanceLogs) }}</span>
                                            <span class="text-xs text-gray-400 ml-1">Entries</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ number_format($sub->price) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                                    @if($sub->status === 'active') bg-green-100 text-green-800 
                                                                                                    @elseif($sub->status === 'expired') bg-gray-100 text-gray-800 
                                                                                                    @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($sub->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openHistory({{ $sub->toJson() }})"
                                                class="text-green-600 hover:text-green-900 mr-3 font-bold uppercase text-[10px] tracking-widest bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded">History</button>
                                            <button @click="openEdit({{ $sub->toJson() }})"
                                                class="text-primary hover:text-primary-hover mr-3">Edit</button>
                                            @if($sub->status === 'active')
                                                <form action="{{ route('manager.subscriptions.update', $sub) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="guest_name"
                                                        value="{{ $sub->user ? $sub->user->name : $sub->guest_name }}">
                                                    <input type="hidden" name="subscription_plan_id"
                                                        value="{{ $sub->subscription_plan_id }}">
                                                    <input type="hidden" name="start_date"
                                                        value="{{ $sub->start_date->format('Y-m-d') }}">
                                                    <input type="hidden" name="end_date"
                                                        value="{{ $sub->end_date->format('Y-m-d') }}">
                                                    <input type="hidden" name="price" value="{{ $sub->price }}">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure you want to cancel this subscription?')">Cancel</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('manager.subscriptions.destroy', $sub) }}" method="POST"
                                                class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-gray-600"
                                                    onclick="return confirm('Delete this record?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No subscriptions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showCreateModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('manager.subscriptions.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Create New Subscription
                            </h3>
                            <div class="mt-4 space-y-4" x-data="createManagerPlanApp()">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client
                                            Name</label>
                                        <input type="text" name="guest_name"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                                            required placeholder="Enter name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone
                                            (Optional)</label>
                                        <input type="text" name="guest_phone"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                                            placeholder="Enter phone">
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service
                                        Plan</label>
                                    <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto pr-1">
                                        <template x-for="plan in plans" :key="plan.id">
                                            <label
                                                class="flex items-center justify-between gap-3 px-4 py-3 rounded-lg border cursor-pointer transition-all"
                                                :class="selectedPlan === plan.id
                                                    ? 'border-primary bg-primary/5 dark:bg-primary/10'
                                                    : 'border-gray-200 dark:border-gray-600 hover:border-primary/50'">
                                                <div class="flex items-center gap-3">
                                                    <input type="radio" name="subscription_plan_id" :value="plan.id"
                                                        class="accent-primary" @change="selectPlan(plan)" required>
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-800 dark:text-white"
                                                            x-text="plan.name"></p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                                            x-text="plan.duration_days + ' Days Access'"></p>
                                                    </div>
                                                </div>
                                                <span class="text-sm font-bold text-primary whitespace-nowrap"
                                                    x-text="Number(plan.price).toLocaleString() + ' RWF'"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                                            Date</label>
                                        <input type="date" name="start_date" x-model="startDate"
                                            @change="updateEndDate()"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End
                                            Date</label>
                                        <input type="date" name="end_date" x-model="endDate"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                                            required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price
                                        (RWF)</label>
                                    <input type="number" name="price" x-model="createPrice"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/20"
                                        placeholder="Auto-filled from plan" required>
                                    <p class="text-xs text-gray-400 mt-1">Auto-filled from selected plan. Edit if
                                        needed.</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Create
                            </button>
                            <button type="button" @click="showCreateModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showEditModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="editUrl" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                Edit Subscription
                            </h3>
                            <div class="mt-2 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client
                                            Name</label>
                                        <input type="text" name="guest_name" x-model="editName"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                        <input type="text" name="guest_phone" x-model="editPhone"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Service
                                        Plan</label>
                                    <select name="subscription_plan_id" x-model="editPlanId"
                                        @change="onEditPlanChange()"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                        required>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }} —
                                                {{ number_format($plan->price) }} RWF
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                                            Date</label>
                                        <input type="date" name="start_date" x-model="editStartDate"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End
                                            Date</label>
                                        <input type="date" name="end_date" x-model="editEndDate"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                            required>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                                        <input type="number" name="price" x-model="editPrice"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <select name="status" x-model="editStatus"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary">
                                            <option value="active">Active</option>
                                            <option value="expired">Expired</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Save Changes
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div x-show="showHistoryModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showHistoryModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                                Attendance History: <span class="text-primary" x-text="currentHistoryClient"></span>
                            </h3>
                            <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-500">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Total Visits: <span class="font-bold text-gray-900 dark:text-white"
                                x-text="attendanceHistory.length"></span>
                        </div>
                        <div class="mt-4">
                            <div
                                class="max-h-[300px] overflow-y-auto border border-gray-100 dark:border-gray-700 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                                Date</th>
                                            <th
                                                class="px-4 py-2 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                                Time</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        <template x-for="log in attendanceHistory" :key="log.id">
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400"
                                                    x-text="new Date(log.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })">
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400"
                                                    x-text="new Date(log.created_at).toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' })">
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="attendanceHistory.length === 0">
                                            <tr>
                                                <td colspan="2"
                                                    class="px-4 py-8 text-center text-gray-400 text-sm italic">No
                                                    attendance recorded yet.</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showHistoryModal = false"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm">
                            Close History
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function managerSubscriptionsApp() {
                return {
                    showCreateModal: false,
                    showEditModal: false,
                    currentSubscription: null,
                    editUrl: '',
                    editName: '',
                    editPhone: '',
                    editPlanId: '',
                    editStartDate: '',
                    editEndDate: '',
                    editPrice: '',
                    editStatus: '',
                    showHistoryModal: false,
                    attendanceHistory: [],
                    currentHistoryClient: '',

                    openEdit: function (sub) {
                        this.currentSubscription = sub;
                        this.editName = sub.user ? sub.user.name : sub.guest_name;
                        this.editPhone = sub.guest_phone || '';
                        this.editPlanId = sub.subscription_plan_id ? String(sub.subscription_plan_id) : (sub.service_id ? String(sub.service_id) : '');
                        this.editStartDate = sub.start_date.split('T')[0];
                        this.editEndDate = sub.end_date.split('T')[0];
                        this.editPrice = sub.price;
                        this.editStatus = sub.status;
                        this.editUrl = '{{ route('manager.subscriptions.update', ':id') }}'.replace(':id', sub.id);
                        this.showEditModal = true;
                    },

                    onEditPlanChange: function () {
                        var id = this.editPlanId;
                        var plans = managerSubscriptionPlans;
                        for (var i = 0; i < plans.length; i++) {
                            if (String(plans[i].id) === String(id)) {
                                this.editPrice = plans[i].price;
                                // Recalculate end date based on new plan duration if start date is set
                                if (this.editStartDate) {
                                    let date = new Date(this.editStartDate);
                                    date.setDate(date.getDate() + parseInt(plans[i].duration_days));
                                    this.editEndDate = date.toISOString().split('T')[0];
                                }
                                break;
                            }
                        }
                    },

                    openHistory: function (sub) {
                        this.currentHistoryClient = sub.user ? sub.user.name : sub.guest_name;
                        this.attendanceHistory = sub.attendance_logs;
                        this.showHistoryModal = true;
                    }
                };
            }

            function createManagerPlanApp() {
                return {
                    selectedPlan: null,
                    createPrice: '',
                    startDate: '{{ date('Y-m-d') }}',
                    endDate: '',
                    plans: managerSubscriptionPlans,
                    selectPlan: function (plan) {
                        this.selectedPlan = plan.id;
                        this.createPrice = plan.price;
                        if (this.startDate && plan.duration_days) {
                            let date = new Date(this.startDate);
                            date.setDate(date.getDate() + parseInt(plan.duration_days));
                            this.endDate = date.toISOString().split('T')[0];
                        }
                    },
                    updateEndDate: function () {
                        if (this.selectedPlan && this.startDate) {
                            let plan = this.plans.find(p => p.id === this.selectedPlan);
                            if (plan) {
                                let date = new Date(this.startDate);
                                date.setDate(date.getDate() + parseInt(plan.duration_days));
                                this.endDate = date.toISOString().split('T')[0];
                            }
                        }
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>