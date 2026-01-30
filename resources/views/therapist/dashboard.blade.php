<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Therapist Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Daily Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                            <div class="text-indigo-600 text-sm font-bold uppercase tracking-wider mb-1">Clients Today</div>
                            <div class="text-2xl font-black text-gray-900">{{ $stats['total_clients'] }}</div>
                        </div>
                        <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                            <div class="text-green-600 text-sm font-bold uppercase tracking-wider mb-1">Revenue Generated</div>
                            <div class="text-2xl font-black text-gray-900">RWF {{ number_format($stats['total_revenue']) }}</div>
                        </div>
                        <div class="bg-orange-50 border border-orange-100 rounded-lg p-4">
                            <div class="text-orange-600 text-sm font-bold uppercase tracking-wider mb-1">Pending</div>
                            <div class="text-2xl font-black text-gray-900">{{ $stats['pending'] }}</div>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4">My Schedule for Today</h3>

                    @if($assignments->isEmpty())
                        <p class="text-gray-500 text-center py-4">No assignments for today.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Time</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Client</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Service</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                @foreach($assignments as $assignment)
                                    <tr x-data="{ showComplete: false }">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $assignment->appointment_time->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $assignment->client_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $assignment->service->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @if($assignment->status === 'completed') bg-green-100 text-green-800 
                                                            @elseif($assignment->status === 'in_progress') bg-blue-100 text-blue-800 
                                                            @elseif($assignment->status === 'cancelled') bg-red-100 text-red-800 
                                                            @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                            </span>
                                            @if($assignment->final_cost)
                                                <div class="text-xs text-gray-500 mt-1">RWF
                                                    {{ number_format($assignment->final_cost) }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($assignment->status === 'pending')
                                                <form method="POST"
                                                    action="{{ route('therapist.assignments.update', $assignment) }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="in_progress">
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900">Start</button>
                                                </form>
                                            @elseif($assignment->status === 'in_progress')
                                                <button @click="showComplete = !showComplete"
                                                    class="text-green-600 hover:text-green-900"
                                                    x-show="!showComplete">Complete...</button>

                                                <form x-show="showComplete" method="POST"
                                                    action="{{ route('therapist.assignments.update', $assignment) }}"
                                                    class="mt-2 p-3 bg-gray-50 rounded border border-gray-200">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="completed">

                                                    <div class="mb-2">
                                                        <label class="block text-xs font-medium text-gray-700">Service
                                                            Performed</label>
                                                        <select name="service_id"
                                                            class="mt-1 block w-full text-xs border-gray-300 rounded-md shadow-sm">
                                                            @foreach($services as $service)
                                                                <option value="{{ $service->id }}" {{ $assignment->service_id == $service->id ? 'selected' : '' }}>
                                                                    {{ $service->name }} ({{ number_format($service->price) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-2">
                                                        <label class="block text-xs font-medium text-gray-700">Final Cost
                                                            (RWF)</label>
                                                        <input type="number" name="final_cost"
                                                            value="{{ $assignment->service->price }}"
                                                            class="mt-1 block w-full text-xs border-gray-300 rounded-md shadow-sm">
                                                    </div>

                                                    <div class="flex gap-2">
                                                        <button type="submit"
                                                            class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">Confirm
                                                            Completion</button>
                                                        <button type="button" @click="showComplete = false"
                                                            class="text-gray-500 text-xs hover:text-gray-700">Cancel</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>