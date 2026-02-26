<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daily Attendance Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Entry Form -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-surface-dark overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-800">
                    <header class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ __('Record Attendance') }}
                            </h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Submit new transaction for the current
                                station.</p>
                        </div>
                        @if($activeAssignment)
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-green-50 dark:bg-green-900/20 rounded border border-green-100 dark:border-green-900/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                <span
                                    class="text-[10px] font-bold text-green-700 dark:text-green-400 uppercase tracking-widest">Assigned
                                    to: {{ $activeAssignment->station->name }}</span>
                            </div>
                        @endif
                    </header>

                    @if(!$activeAssignment)
                        <div class="mt-6 bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <span class="material-symbols-outlined text-orange-400">warning</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-orange-700 dark:text-orange-300 font-medium">
                                        {{ __('No active shift assignment found.') }}
                                    </p>
                                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                        {{ __('Please contact your manager to be assigned to a station for the current time.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="post" action="{{ route('receptionist.store') }}"
                        class="mt-8 space-y-6 @if(!$activeAssignment) opacity-40 pointer-events-none grayscale @endif">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Station Selection (Fixed if assigned) -->
                            <div>
                                <x-input-label for="station_id" :value="__('Active Station')" />
                                <select id="station_id" name="station_id"
                                    class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm @if($activeAssignment) bg-gray-50 dark:bg-gray-800 cursor-not-allowed @endif"
                                    required @if($activeAssignment) readonly @endif>
                                    @if(!$activeAssignment)
                                        <option value="">No Station Assigned</option>
                                    @else
                                        <option value="{{ $activeAssignment->station->id }}" selected>
                                            {{ $activeAssignment->station->name }}</option>
                                    @endif
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('station_id')" />
                            </div>

                            <!-- Service Selection -->
                            @if($isGym)
                                {{-- GYM: auto-select gym service silently --}}
                                @if($gymService)
                                    <input type="hidden" name="service_id" value="{{ $gymService->id }}">
                                @endif
                            @elseif($isSaunaOrMassage)
                                {{-- SAUNA / MASSAGE STATION: pick service type via buttons --}}
                                <div class="md:col-span-2">
                                    <x-input-label :value="__('Service Type')" />
                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @if($saunaService)
                                            <label
                                                class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-colors">
                                                <input type="radio" name="service_id" value="{{ $saunaService->id }}"
                                                    class="accent-primary" {{ old('service_id') == $saunaService->id ? 'checked' : '' }} required>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Sauna Only
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ number_format($saunaService->price) }}
                                                        RWF</p>
                                                </div>
                                            </label>
                                        @endif

                                        @if($massageService)
                                            <label
                                                class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-colors">
                                                <input type="radio" name="service_id" value="{{ $massageService->id }}"
                                                    class="accent-primary" {{ old('service_id') == $massageService->id ? 'checked' : '' }}>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Massage Only
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ number_format($massageService->price) }}
                                                        RWF</p>
                                                </div>
                                            </label>
                                        @endif

                                        {{-- Sauna & Massage combined --}}
                                        @if($saunaMassageService)
                                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-colors">
                                                <input type="radio" name="service_id" value="{{ $saunaMassageService->id }}"
                                                    class="accent-primary" {{ old('service_id') == $saunaMassageService->id ? 'checked' : '' }}>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">Sauna &amp; Massage</p>
                                                    <p class="text-xs text-gray-500">{{ number_format($saunaMassageService->price) }} RWF</p>
                                                </div>
                                            </label>
                                        @else
                                            <label class="flex items-center gap-3 p-3 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 opacity-50 cursor-not-allowed" title="Add a 'Sauna &amp; Massage' service in admin to enable this option">
                                                <input type="radio" name="service_id" value="" class="accent-primary" disabled>
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Sauna &amp; Massage</p>
                                                    <p class="text-xs text-orange-500">Service not configured — contact admin</p>
                                                </div>
                                            </label>
                                        @endif

                                        @if(!$saunaService && !$massageService)
                                            <p class="text-xs text-orange-600 dark:text-orange-400 md:col-span-3">No sauna/massage services found. Please contact admin.</p>
                                        @endif
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('service_id')" />
                                </div>
                            @else
                                {{-- OTHER STATIONS: show relevant service dropdown --}}
                                <div>
                                    <x-input-label for="service_id" :value="__('Service / Entrance Type')" />
                                    <select id="service_id" name="service_id"
                                        class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
                                        required>
                                        <option value="">Select Service...</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} ({{ number_format($service->price) }} RWF)
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('service_id')" />
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- User Count -->
                            <div>
                                <x-input-label for="user_count" :value="__('Number of Users')" />
                                <x-text-input id="user_count" name="user_count" type="number" min="1"
                                    class="mt-1 block w-full" :value="old('user_count', 1)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('user_count')" />
                            </div>

                            <!-- Payment Method -->
                            <div x-data="{ 
                                method: 'Cash', 
                                verifyCode: '', 
                                verificationResult: null,
                                loading: false,
                                message: '',
                                
                                get isVerified() {
                                    if (this.method === 'Cash' || this.method === 'Mobile' || this.method === 'Signature') return true;
                                    return this.verificationResult !== null;
                                },
                                
                                async checkCode() {
                                    if (!this.verifyCode) return;
                                    this.loading = true;
                                    this.verificationResult = null;
                                    this.message = '';
                                    
                                    try {
                                        const type = (this.method === 'Signature' || this.method === 'Subscription') ? 'subscription' : 'ticket';
                                        const response = await fetch(`{{ route('receptionist.verify') }}?code=${this.verifyCode}&type=${type}`);
                                        const data = await response.json();
                                        
                                        if (data.success) {
                                            this.verificationResult = data;
                                            document.getElementById('user_count').value = 1;
                                            document.getElementById('amount').value = data.price;
                                            document.getElementById('amount').readOnly = true;
                                            document.getElementById('amount-field').style.display = 'block';
                                        } else {
                                            this.message = data.message;
                                        }
                                    } catch (e) {
                                        this.message = 'Verification failed. Please try again.';
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <div>
                                    <x-input-label for="payment_method" :value="__('Entry Category')" />
                                    <select id="payment_method" name="payment_method" x-model="method"
                                        class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
                                        required
                                        @change="verificationResult = null; verifyCode = ''; message = ''; toggleAmountField(); if(method === 'Cash' || method === 'Mobile') { document.getElementById('amount').readOnly = false; }">
                                        @if($isGym)
                                            <option value="Subscription">Active Subscription</option>
                                            <option value="Signature">Institution / Signing</option>
                                            <option value="Ticket">Ticket User</option>
                                            <option value="Cash">Pay per Session (Cash)</option>
                                            <option value="Mobile">Pay per Session (Momo)</option>
                                        @else
                                            <option value="Subscription">Monthly Subscription</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Mobile">Mobile Money</option>
                                            <option value="Signature">Contract / Signature</option>
                                            <option value="Ticket">Prepaid Ticket</option>
                                        @endif
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                                </div>

                                <!-- Verification Field -->
                                <div x-show="method === 'Ticket' || method === 'Subscription'"
                                    class="mt-4 p-4 bg-primary/5 dark:bg-primary/10 rounded-lg border border-primary/20"
                                    style="display: none;">
                                    <div class="flex items-end gap-2">
                                        <div class="flex-1">
                                            <label
                                                class="block text-[10px] font-bold text-primary uppercase tracking-widest mb-1"
                                                x-text="method === 'Ticket' ? 'Enter Ticket Code' : 'Enter Subscription ID / Phone'"></label>
                                            <input type="text" x-model="verifyCode" @keyup.enter="checkCode()"
                                                class="block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm focus:border-primary focus:ring-primary text-sm"
                                                placeholder="Enter ID or Phone Number...">
                                        </div>
                                        <button type="button" @click="checkCode()" :disabled="loading"
                                            class="h-[38px] px-4 bg-primary hover:bg-primary-hover text-white rounded-md text-xs font-bold uppercase transition-colors disabled:opacity-50">
                                            <span x-show="!loading">Verify</span>
                                            <span x-show="loading" class="animate-pulse">Checking...</span>
                                        </button>
                                    </div>

                                    <!-- Results -->
                                    <div class="mt-3">
                                        <template x-if="verificationResult">
                                            <div class="flex items-start gap-2 text-green-600 dark:text-green-400">
                                                <span class="material-symbols-outlined text-[18px]">verified</span>
                                                <div class="text-xs">
                                                    <p class="font-bold" x-text="verificationResult.name"></p>
                                                    <p x-text="verificationResult.service"></p>
                                                    <p x-show="verificationResult.expires"
                                                        class="mt-1 text-[10px] opacity-75">Valid until: <span
                                                            x-text="verificationResult.expires"></span></p>
                                                    <input type="hidden" name="subscription_id"
                                                        :value="verificationResult.type === 'subscription' ? verificationResult.id : ''">
                                                    <input type="hidden" name="ticket_item_id"
                                                        :value="verificationResult.type === 'ticket' ? verificationResult.id : ''">
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="message">
                                            <div class="flex items-center gap-2 text-red-600 dark:text-red-400 text-xs">
                                                <span class="material-symbols-outlined text-[18px]">error</span>
                                                <span x-text="message"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col md:flex-row md:items-center justify-between pt-6 border-t border-gray-100 dark:border-gray-800 mt-6 col-span-full gap-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-primary-button class="h-11 px-8 w-full md:w-auto" ::disabled="!isVerified">
                                            <span class="material-symbols-outlined text-[20px] mr-2">save</span>
                                            {{ __('Record Entry') }}
                                        </x-primary-button>

                                        @if (session('success'))
                                            <div
                                                class="flex items-center gap-1 text-green-600 font-bold text-xs uppercase tracking-wide px-3 h-11">
                                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                    </div>

                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center md:text-right"
                                        x-show="!isVerified">
                                        Verification Required for this category
                                    </p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center md:text-right"
                                        x-show="isVerified">
                                        Transaction will be saved as draft
                                    </p>
                                </div>
                            </div>

                            <!-- Amount (Unit Price) -->
                            <div id="amount-field">
                                <x-input-label for="amount" :value="__('Collection Amount (RWF / Person)')" />
                                <x-text-input id="amount" name="amount" type="number"
                                    class="mt-1 block w-full bg-gray-50/50 dark:bg-gray-800/50" :value="old('amount')"
                                    placeholder="0" />
                                <x-input-error class="mt-2" :messages="$errors->get('amount')" />
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Recent Entries Side Panel -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Entries</h3>
                    <div class="flow-root">
                        <ul role="list" class="-my-5 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentEntries as $entry)
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $entry->service->name }} (x{{ $entry->user_count }})
                                            </p>
                                            <p class="text-sm text-gray-500 truncate">
                                                {{ $entry->payment_method }}
                                            </p>
                                        </div>
                                        <div
                                            class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($entry->amount) }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function toggleAmountField() {
                const method = document.getElementById('payment_method').value;
                const amountField = document.getElementById('amount-field');
                const amountInput = document.getElementById('amount');

                if (method === 'Cash' || method === 'Mobile') {
                    amountField.style.display = 'block';
                    amountInput.required = true;
                    amountInput.style.backgroundColor = 'transparent';
                } else {
                    amountField.style.display = 'none';
                    amountInput.required = false;
                    amountInput.value = '';
                }
            }

            document.addEventListener('DOMContentLoaded', toggleAmountField);
        </script>
    @endpush
</x-app-layout>