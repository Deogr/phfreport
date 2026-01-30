<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6">Editing Entry for {{ $log->created_at->format('H:i') }}</h3>

                <form method="POST" action="{{ route('receptionist.logs.update', $log) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Service Selection -->
                        <div>
                            <x-input-label for="service_id" :value="__('Service')" />
                            <select id="service_id" name="service_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ $log->service_id == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} ({{ number_format($service->price) }} RWF)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                        </div>

                        <!-- User Count -->
                        <div>
                            <x-input-label for="user_count" :value="__('Number of Guests')" />
                            <x-text-input id="user_count" class="block mt-1 w-full" type="number" name="user_count" :value="old('user_count', $log->user_count)" required min="1" max="100" />
                            <x-input-error :messages="$errors->get('user_count')" class="mt-2" />
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <x-input-label for="payment_method" :value="__('Payment Method')" />
                            <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="Cash" {{ $log->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Mobile" {{ $log->payment_method == 'Mobile' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="Signature" {{ $log->payment_method == 'Signature' ? 'selected' : '' }}>Signature / Hotel Guest</option>
                                <option value="Ticket" {{ $log->payment_method == 'Ticket' ? 'selected' : '' }}>Prepaid Ticket</option>
                                <option value="Subscription" {{ $log->payment_method == 'Subscription' ? 'selected' : '' }}>Subscription</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>

                        <!-- Manual Amount (Only for Cash/Mobile) -->
                        <div x-data="{ show: '{{ $log->payment_method }}' === 'Cash' || '{{ $log->payment_method }}' === 'Mobile' }" 
                             x-show="show" 
                             x-on:change="show = ['Cash', 'Mobile'].includes($event.target.value)" 
                             class="transaction-amount-field">
                            <x-input-label for="amount" :value="__('Amount Collected (Per User)')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" :value="old('amount', $log->unit_price)" />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Leave blank to use default service price.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Update Entry') }}</x-primary-button>
                        <a href="{{ route('receptionist.summary') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 uppercase font-bold tracking-wider">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
