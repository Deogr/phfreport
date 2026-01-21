<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 dark:text-white leading-tight tracking-tight">
            {{ __('Personnel Scheduling') }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="{ 
        editMode: false, 
        currentAssignment: null,
        editStation: '',
        editStartTime: '',
        editEndTime: '',
        editDate: '',
        editDay: '',
        editType: 'recurring',
        actionUrl: '',
        
        openEdit(assignment) {
            this.currentAssignment = assignment;
            this.editStation = assignment.station_id;
            this.editStartTime = assignment.start_time.substring(0, 5);
            this.editEndTime = assignment.end_time.substring(0, 5);
            this.editDate = assignment.assignment_date || '';
            this.editDay = assignment.day_of_week || '';
            this.editType = assignment.assignment_date ? 'once' : 'recurring';
            this.actionUrl = '{{ route($routeBase . '.update', ':id') }}'.replace(':id', assignment.id);
            this.editMode = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Assignment Form -->
                <div
                    class="lg:col-span-1 bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-800 p-6">
                    <header class="mb-6">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('Assign New Shift') }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest font-bold">Duty Roster Management
                        </p>
                    </header>

                    <form method="post" action="{{ route($routeBase . '.store') }}" class="space-y-5">
                        @csrf

                        <!-- Staff Selection -->
                        <div>
                            <x-input-label for="staff" :value="__('Receptionist')"
                                class="text-xs font-bold uppercase text-gray-400 mb-1.5" />
                            <select id="staff" name="staff"
                                class="w-full bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-all"
                                required>
                                <option value="">Select staff member...</option>
                                @foreach($staff as $user)
                                    <option value="{{ $user->id }}" {{ old('staff') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('staff')" />
                        </div>

                        <!-- Station Selection -->
                        <div>
                            <x-input-label for="station" :value="__('Deployment Station')"
                                class="text-xs font-bold uppercase text-gray-400 mb-1.5" />
                            <select id="station" name="station"
                                class="w-full bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 rounded-lg text-sm focus:ring-primary focus:border-primary transition-all"
                                required>
                                <option value="">Select location...</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}" {{ old('station') == $station->id ? 'selected' : '' }}>
                                        {{ $station->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('station')" />
                        </div>

                        <!-- Scheduling Type -->
                        <div x-data="{ mode: 'recurring' }">
                            <div class="flex p-1 bg-gray-100 dark:bg-white/5 rounded-lg mb-4">
                                <button type="button" @click="mode = 'recurring'" 
                                    :class="mode === 'recurring' ? 'bg-white dark:bg-gray-800 shadow-sm text-primary' : 'text-gray-500'"
                                    class="flex-1 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all">Recurring</button>
                                <button type="button" @click="mode = 'once'" 
                                    :class="mode === 'once' ? 'bg-white dark:bg-gray-800 shadow-sm text-primary' : 'text-gray-500'"
                                    class="flex-1 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all">Specific Date</button>
                            </div>

                            <div x-show="mode === 'once'">
                                <x-input-label for="assignment_date" :value="__('Select Date')"
                                    class="text-xs font-bold uppercase text-gray-400 mb-1.5" />
                                <x-text-input id="assignment_date" name="assignment_date" type="date" class="w-full text-sm"
                                    :value="old('assignment_date')" />
                                <x-input-error class="mt-2" :messages="$errors->get('assignment_date')" />
                            </div>

                            <div x-show="mode === 'recurring'">
                                <x-input-label class="text-xs font-bold uppercase text-gray-400 mb-3"
                                    :value="__('Weekly Recurrence')" />
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach(['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $day)
                                        <label
                                            class="relative flex flex-col items-center justify-center p-2 rounded-lg border border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-all group has-[:checked]:bg-primary/5 has-[:checked]:border-primary">
                                            <input type="checkbox" name="days[]" value="{{ $day }}" class="peer sr-only">
                                            <span
                                                class="text-[10px] font-bold text-gray-500 dark:text-gray-400 group-hover:text-primary peer-checked:text-primary">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('days')" />
                            </div>
                        </div>

                        <!-- Time Selection -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="startTime" :value="__('Start')"
                                    class="text-xs font-bold uppercase text-gray-400 mb-1.5" />
                                <x-text-input id="startTime" name="startTime" type="time" class="w-full text-sm"
                                    :value="old('startTime')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('startTime')" />
                            </div>

                            <div>
                                <x-input-label for="endTime" :value="__('End')"
                                    class="text-xs font-bold uppercase text-gray-400 mb-1.5" />
                                <x-text-input id="endTime" name="endTime" type="time" class="w-full text-sm"
                                    :value="old('endTime')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('endTime')" />
                            </div>
                        </div>

                        <div class="pt-4">
                            <x-primary-button
                                class="w-full h-11 flex justify-center text-xs font-bold uppercase tracking-widest shadow-lg shadow-primary/20">
                                {{ __('Assign Shift') }}
                            </x-primary-button>

                            @if (session('success'))
                                <p class="mt-3 text-[10px] font-bold text-green-600 text-center uppercase tracking-widest">
                                    {{ session('success') }}</p>
                            @endif
                            @if (session('error'))
                                <p class="mt-3 text-[10px] font-bold text-red-600 text-center uppercase tracking-widest">
                                    {{ session('error') }}</p>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Active Schedule Table -->
                <div
                    class="lg:col-span-2 bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-xl border border-gray-200 dark:border-gray-800">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Active Deployments</h3>
                            <p class="text-xs text-gray-500 mt-1 uppercase tracking-widest font-bold">Live Schedule View
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50/50 dark:bg-gray-800/20 border-b border-gray-100 dark:border-gray-800">
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Receptionist</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                        Station</th>
                                    <th
                                        class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">
                                        Schedule</th>
                                    <th class="p-4 text-right"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($assignments as $assignment)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-all group">
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">
                                                    {{ substr($assignment->user->name, 0, 2) }}
                                                </div>
                                                <span
                                                    class="text-sm font-bold text-gray-900 dark:text-white">{{ $assignment->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="material-symbols-outlined text-gray-400 text-[18px]">location_on</span>
                                                <span
                                                    class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $assignment->station->name }}</span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center">
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="px-2 py-0.5 rounded {{ ($assignment->assignment_date == date('Y-m-d') || (!$assignment->assignment_date && $assignment->day_of_week == strtoupper(date('D')))) ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' }} text-[10px] font-black uppercase tracking-widest">
                                                    @if($assignment->assignment_date)
                                                        {{ \Carbon\Carbon::parse($assignment->assignment_date)->format('M d, Y') }}
                                                    @else
                                                        {{ $assignment->day_of_week }}
                                                    @endif
                                                </span>
                                                @if($assignment->assignment_date == date('Y-m-d') || (!$assignment->assignment_date && $assignment->day_of_week == strtoupper(date('D'))))
                                                    <span class="text-[9px] text-green-500 font-bold mt-1 uppercase tracking-tighter">Active Today</span>
                                                @endif
                                                <span class="text-xs font-mono text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($assignment->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($assignment->end_time)->format('H:i') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="p-4 text-right">
                                            <div
                                                class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="openEdit({{ $assignment }})"
                                                    class="size-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-primary hover:bg-primary/10 transition-all">
                                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                                </button>

                                                <form method="POST"
                                                    action="{{ route($routeBase . '.destroy', $assignment) }}"
                                                    onsubmit="return confirm('Remove this assignment?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="size-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-all">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <x-modal name="edit-assignment" x-show="editMode" @close="editMode = false" focusable>
            <div class="p-6">
                <header class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Edit Shift</h2>
                        <p class="text-sm text-gray-500 mt-1">Adjust timing or station for <span
                                class="font-bold text-primary" x-text="currentAssignment?.user.name"></span> on <span
                                class="font-bold text-primary" x-text="currentAssignment?.assignment_date ? currentAssignment.assignment_date : currentAssignment?.day_of_week"></span></p>
                    </div>
                    <button @click="editMode = false" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </header>

                <form :action="actionUrl" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="editType" :value="__('Scheduling Type')" 
                                class="text-xs font-bold uppercase text-gray-400 mb-1.5" />
                            <div class="flex p-1 bg-gray-100 dark:bg-white/5 rounded-lg">
                                <button type="button" @click="editType = 'recurring'" 
                                    :class="editType === 'recurring' ? 'bg-white dark:bg-gray-800 shadow-sm text-primary' : 'text-gray-500'"
                                    class="flex-1 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all">Recurring</button>
                                <button type="button" @click="editType = 'once'" 
                                    :class="editType === 'once' ? 'bg-white dark:bg-gray-800 shadow-sm text-primary' : 'text-gray-500'"
                                    class="flex-1 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-md transition-all">Specific Date</button>
                            </div>
                        </div>
                        
                        <div>
                            <div x-show="editType === 'once'">
                                <x-input-label for="editDate" :value="__('Change Date')" />
                                <x-text-input id="editDate" name="assignment_date" type="date" class="mt-1 block w-full"
                                    x-model="editDate" ::required="editType === 'once'" />
                            </div>
                            <div x-show="editType === 'recurring'">
                                <x-input-label for="editDay" :value="__('Day of Week')" />
                                <select id="editDay" name="day_of_week" x-model="editDay"
                                    class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
                                    ::required="editType === 'recurring'">
                                    @foreach(['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $day)
                                        <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-input-label for="editStation" :value="__('Change Station')" />
                            <select id="editStation" name="station_id" x-model="editStation"
                                class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm"
                                required>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4 col-span-2">
                            <div>
                                <x-input-label for="editStartTime" :value="__('Starts')" />
                                <x-text-input id="editStartTime" name="startTime" type="time" class="mt-1 block w-full"
                                    x-model="editStartTime" required />
                            </div>
                            <div>
                                <x-input-label for="editEndTime" :value="__('Ends')" />
                                <x-text-input id="editEndTime" name="endTime" type="time" class="mt-1 block w-full"
                                    x-model="editEndTime" required />
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields to clear the other mode when switching -->
                    <template x-if="editType === 'recurring'">
                        <input type="hidden" name="assignment_date" value="">
                    </template>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="editMode = false"
                            class="px-4 py-2 text-sm font-bold text-gray-500 uppercase tracking-widest hover:text-gray-700">Cancel</button>
                        <x-primary-button>Update Assignment</x-primary-button>
                    </div>
                </form>
            </div>
        </x-modal>
    </div>
</x-app-layout>