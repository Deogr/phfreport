<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ticket Sales Management') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ 
        showCreateModal: false, 
        quantity: 1, 
        pricePerTicket: 3000,
        showEditModal: false,
        editUrl: '',
        editName: '',
        editPhone: '',
        editQty: 1,
        editPrice: 3000,
        editPayment: 'cash',
        editStatus: 'paid',
        showItemsModal: false,
        modalTicketId: null,
        modalItems: [],

        openEdit(ticket) {
            this.editName = ticket.guest_name;
            this.editPhone = ticket.guest_phone || '';
            this.editQty = ticket.quantity;
            this.editPrice = ticket.price_per_ticket;
            this.editPayment = ticket.payment_method;
            this.editStatus = ticket.status;
            this.editUrl = '{{ route('admin.tickets.update', ':id') }}'.replace(':id', ticket.id);
            this.showEditModal = true;
        },

        openItemsModal(id, items) {
            this.modalTicketId = id;
            this.modalItems = items;
            this.showItemsModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Actions -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div
                    class="flex flex-wrap items-center p-1 bg-gray-100 dark:bg-gray-700/50 rounded-lg w-full lg:w-auto">
                    @php $currentStatus = request('status', 'all'); @endphp
                    <a href="{{ route('admin.tickets', ['status' => 'all']) }}"
                        class="flex-1 lg:flex-none text-center px-4 py-2 text-sm font-bold rounded-md transition-all duration-200 {{ $currentStatus === 'all' ? 'bg-white dark:bg-gray-800 text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                        All <span
                            class="ml-1 px-2 py-0.5 text-[10px] bg-gray-200 dark:bg-gray-700 rounded-full">{{ $counts['all'] }}</span>
                    </a>
                    <a href="{{ route('admin.tickets', ['status' => 'paid']) }}"
                        class="flex-1 lg:flex-none text-center px-4 py-2 text-sm font-bold rounded-md transition-all duration-200 {{ $currentStatus === 'paid' ? 'bg-white dark:bg-gray-800 text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                        Paid <span
                            class="ml-1 px-2 py-0.5 text-[10px] bg-green-100 text-green-700 rounded-full">{{ $counts['paid'] }}</span>
                    </a>
                    <a href="{{ route('admin.tickets', ['status' => 'used']) }}"
                        class="flex-1 lg:flex-none text-center px-4 py-2 text-sm font-bold rounded-md transition-all duration-200 {{ $currentStatus === 'used' ? 'bg-white dark:bg-gray-800 text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                        Used <span
                            class="ml-1 px-2 py-0.5 text-[10px] bg-blue-100 text-blue-700 rounded-full">{{ $counts['used'] }}</span>
                    </a>
                    <a href="{{ route('admin.tickets', ['status' => 'cancelled']) }}"
                        class="flex-1 lg:flex-none text-center px-4 py-2 text-sm font-bold rounded-md transition-all duration-200 {{ $currentStatus === 'cancelled' ? 'bg-white dark:bg-gray-800 text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                        Cancelled <span
                            class="ml-1 px-2 py-0.5 text-[10px] bg-red-100 text-red-700 rounded-full">{{ $counts['cancelled'] }}</span>
                    </a>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                    <a href="{{ route('admin.tickets.items') }}"
                        class="text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-all duration-200">
                        View Item Registry
                    </a>
                    <button @click="showCreateModal = true"
                        class="text-center bg-primary hover:bg-primary-hover text-white font-bold py-2 px-4 rounded shadow-sm transition-all duration-200">
                        Sell New Ticket(s)
                    </button>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-bold mb-4 capitalize">{{ request('status', 'all') }} Sales History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Buyer</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">
                                        Type</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Qty</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
                                        Redeemed</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">
                                        Method</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden md:table-cell">
                                            #{{ $ticket->id }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $ticket->guest_name }}
                                            @if($ticket->guest_phone)
                                                <span class="text-xs text-gray-400 block hidden sm:block">{{ $ticket->guest_phone }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden lg:table-cell">
                                            {{ ucfirst($ticket->type) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $ticket->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-bold">
                                            RWF {{ number_format($ticket->total_amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                            <div class="flex items-center">
                                                <span
                                                    class="text-sm font-bold {{ $ticket->used_count == $ticket->quantity ? 'text-blue-600' : 'text-green-600' }}">
                                                    {{ $ticket->used_count }} / {{ $ticket->quantity }}
                                                </span>
                                                @if($ticket->used_count > 0)
                                                    <button
                                                        @click="openItemsModal({{ $ticket->id }}, {{ json_encode($ticket->items) }})"
                                                        class="ml-2 text-[10px] bg-gray-100 dark:bg-gray-700 px-1.5 rounded hover:bg-gray-200 transition-colors">
                                                        Details
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 uppercase hidden lg:table-cell">
                                            {{ $ticket->payment_method }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden sm:table-cell">
                                            {{ $ticket->created_at->format('M d, H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                                                                @if($ticket->status === 'paid') bg-green-100 text-green-800 
                                                                                                                @elseif($ticket->status === 'used') bg-blue-100 text-blue-800 
                                                                                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($ticket->quantity > 1)
                                                <a href="{{ route('admin.tickets.export', $ticket) }}" target="_blank"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3">Export</a>
                                            @endif
                                            <a href="{{ route('admin.tickets.print', $ticket) }}" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Print</a>
                                            <button @click="openEdit({{ $ticket->toJson() }})"
                                                class="text-primary hover:text-primary-hover mr-3">Edit</button>
                                            <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('Delete this ticket record?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No sales recorded.</td>
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
                    <form action="{{ route('admin.tickets.store') }}" method="POST">
                        @csrf
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Record New Sale
                            </h3>

                            <div class="mt-4 space-y-4">
                                <!-- Client Info -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client
                                            Name</label>
                                        <input type="text" name="guest_name"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            required placeholder="Enter name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone
                                            (Optional)</label>
                                        <input type="text" name="guest_phone"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                </div>

                                <!-- Ticket Details -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                        <input type="number" name="quantity" x-model="quantity" min="1"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price/Ticket
                                            (RWF)</label>
                                        <input type="number" name="price_per_ticket" x-model="pricePerTicket" min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                            required>
                                    </div>
                                </div>

                                <!-- Total & Payment -->
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Total
                                            Amount:</span>
                                        <span class="text-xl font-bold text-primary">RWF <span
                                                x-text="(quantity * pricePerTicket).toLocaleString()"></span></span>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment
                                            Method</label>
                                        <select name="payment_method"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="cash">Cash</option>
                                            <option value="momo">Mobile Money</option>
                                            <option value="card">Card</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Confirm & Print
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
                                Edit Ticket Sale
                            </h3>

                            <div class="mt-4 space-y-4">
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

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity</label>
                                        <input type="number" name="quantity" x-model="editQty" min="1"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price/Ticket</label>
                                        <input type="number" name="price_per_ticket" x-model="editPrice" min="0"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary"
                                            required>
                                    </div>
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">New
                                            Total:</span>
                                        <span class="text-xl font-bold text-primary">RWF <span
                                                x-text="(editQty * editPrice).toLocaleString()"></span></span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Method</label>
                                            <select name="payment_method" x-model="editPayment"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="cash">Cash</option>
                                                <option value="momo">Momo</option>
                                                <option value="card">Card</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                            <select name="status" x-model="editStatus"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-primary focus:border-primary">
                                                <option value="paid">Paid</option>
                                                <option value="used">Used</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-hover focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Update Sale
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

        <!-- Items Modal -->
        <div x-show="showItemsModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showItemsModal = false">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Ticket Items for #<span x-text="modalTicketId"></span></h3>
                        <div class="max-h-96 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code
                                        </th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    <template x-for="item in modalItems" :key="item.id">
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-mono" x-text="item.code"></td>
                                            <td class="px-4 py-2">
                                                <span x-text="item.is_used ? 'USED' : 'VALID'" :class="{
                                                          'px-2 py-0.5 rounded text-[10px] font-bold': true,
                                                          'bg-green-100 text-green-800': !item.is_used,
                                                          'bg-blue-100 text-blue-800': item.is_used
                                                      }"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 text-right">
                            <button @click="showItemsModal = false"
                                class="bg-gray-100 px-4 py-2 rounded text-sm font-bold">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>