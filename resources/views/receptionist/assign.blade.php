<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('receptionist.assign.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="client_name" :value="__('Client Name')" />
                            <x-text-input id="client_name" name="client_name" type="text" class="mt-1 block w-full"
                                :value="old('client_name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('client_name')" />
                        </div>

                        <div>
                            <x-input-label for="service_id" :value="__('Service')" />
                            <select id="service_id" name="service_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('service_id')" />
                        </div>

                        <div>
                            <x-input-label for="therapist_id" :value="__('Therapist')" />
                            <select id="therapist_id" name="therapist_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required>
                                <option value="">Select Therapist</option>
                                @foreach($therapists as $therapist)
                                    <option value="{{ $therapist->id }}">{{ $therapist->name }} ({{ $therapist->status }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('therapist_id')" />
                        </div>

                        <div>
                            <x-input-label for="appointment_time" :value="__('Appointment Time')" />
                            <x-text-input id="appointment_time" name="appointment_time" type="time"
                                class="mt-1 block w-full" :value="now()->format('H:i')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('appointment_time')" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3">{{ old('notes') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Assign Client') }}</x-primary-button>

                            @if (session('success'))
                                <p x-data="{ show: true }" x-show="show" x-transition
                                    x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">
                                    {{ session('success') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>