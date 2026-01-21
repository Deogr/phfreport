<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Admin</a>
            <span class="mx-2 text-gray-300">/</span>
            <span class="font-medium text-gray-900 dark:text-white">Station Configuration</span>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        editMode: false,
        currentStation: null,
        editName: '',
        editLocation: '',
        editStatus: '',
        editUrl: '',

        openEdit(station) {
            this.currentStation = station;
            this.editName = station.name;
            this.editLocation = station.location || '';
            this.editStatus = station.status;
            this.editUrl = '{{ route('admin.stations.update', ':id') }}'.replace(':id', station.id);
            this.editMode = true;
            this.$dispatch('open-modal', 'edit-station');
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Page Heading -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">
                        NODE REGISTRY
                    </h2>
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 mt-1 uppercase tracking-[0.2em]">Terminal & Node Infrastructure</p>
                </div>
                <div class="flex items-center gap-3">
                    <button x-on:click="$dispatch('open-modal', 'create-station')"
                        class="group flex items-center justify-center h-12 px-8 rounded-xl bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-primary-hover transition-all shadow-xl shadow-primary/20">
                        <span class="material-symbols-outlined text-[20px] mr-2 group-hover:rotate-90 transition-transform">add_circle</span>
                        Register New Node
                    </button>
                </div>
            </div>

            <x-modal name="create-station" focusable>
                <form method="post" action="{{ route('admin.stations.store') }}" class="p-8">
                    @csrf
                    <div class="flex items-center gap-4 mb-8">
                        <div class="size-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">add_box</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                                {{ __('DEPLOY NEW NODE') }}
                            </h2>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Infrastructure Expansion</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" :value="__('Descriptive Name')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="name" name="name" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800" :value="old('name')"
                                required placeholder="e.g. South Wing Turnstile" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="location" :value="__('Physical Location')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <x-text-input id="location" name="location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                    :value="old('location')" placeholder="e.g. Ground Floor" />
                                <x-input-error class="mt-2" :messages="$errors->get('location')" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Operational Status')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <select id="status" name="status"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                    <option value="active">Active / Operational</option>
                                    <option value="inactive">Under Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end gap-3 border-t border-gray-50 dark:border-gray-800 pt-6">
                        <x-secondary-button x-on:click="$dispatch('close')" class="text-[10px] font-black uppercase tracking-widest h-11 px-8">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button class="h-11 px-8 text-[10px] font-black uppercase tracking-widest">
                            {{ __('Confirm Deployment') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-modal>

            <!-- Edit Station Modal -->
            <x-modal name="edit-station" x-show="editMode" @close="editMode = false" focusable>
                <form :action="editUrl" method="post" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4 mb-8">
                        <div class="size-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">edit_note</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                                {{ __('CONFIGURE NODE') }}
                            </h2>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">STA-<span x-text="currentStation?.id"></span> System Settings</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="edit_name" :value="__('Descriptive Name')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="edit_name" name="name" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                x-model="editName" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="edit_location" :value="__('Physical Location')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <x-text-input id="edit_location" name="location" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                    x-model="editLocation" />
                            </div>

                            <div>
                                <x-input-label for="edit_status" :value="__('Operational Status')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                                <select id="edit_status" name="status" x-model="editStatus"
                                    class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                    <option value="active">Active / Operational</option>
                                    <option value="inactive">Under Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end gap-3 border-t border-gray-50 dark:border-gray-800 pt-6">
                        <x-secondary-button @click="editMode = false" class="text-[10px] font-black uppercase tracking-widest h-11 px-8">
                            {{ __('Dismiss') }}
                        </x-secondary-button>
                        <x-primary-button class="h-11 px-8 text-[10px] font-black uppercase tracking-widest">
                            {{ __('Save Changes') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-modal>

            <!-- Stations Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
                @foreach($stations as $station)
                    <div class="bg-white dark:bg-surface-dark p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/40 dark:shadow-none flex flex-col gap-8 transition-all hover:scale-[1.03] group relative overflow-hidden">

                        <!-- Status Indicator -->
                        <div class="flex justify-between items-start">
                            <div class="size-14 rounded-2xl bg-primary/5 dark:bg-white/5 flex items-center justify-center text-primary border border-primary/10 group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-lg shadow-primary/5">
                                <span class="material-symbols-outlined text-[32px]">hub</span>
                            </div>
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $station->status === 'active' ? 'bg-green-50 text-green-700 ring-1 ring-green-100 dark:bg-green-900/20 dark:text-green-400 dark:ring-0' : 'bg-orange-50 text-orange-700 ring-1 ring-orange-100 dark:bg-orange-900/20 dark:text-orange-400 dark:ring-0' }}">
                                <span class="size-2 rounded-full mr-2 {{ $station->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-orange-500' }}"></span>
                                {{ $station->status === 'active' ? 'Operational' : 'Maintenance' }}
                            </span>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-tighter bg-gray-100 dark:bg-gray-800 text-gray-500">UID: STA-{{ str_pad($station->id, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight group-hover:text-primary transition-colors">
                                {{ $station->name }}
                            </h3>
                            <p class="text-xs font-bold text-gray-400 dark:text-gray-500 mt-2 flex items-center gap-1.5 uppercase tracking-widest">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                {{ $station->location ?? 'General Facility' }}
                            </p>
                        </div>

                        <div class="mt-auto grid grid-cols-2 gap-4 pt-6 border-t border-gray-50 dark:border-gray-800/50">
                            <!-- Toggle Status -->
                            <form method="POST" action="{{ route('admin.stations.toggle-status', $station) }}" class="w-full">
                                @csrf
                                <input type="hidden" name="status" value="{{ $station->status === 'active' ? 'inactive' : 'active' }}">
                                <button type="submit"
                                    class="w-full h-12 rounded-2xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 {{ $station->status === 'active' ? 'hover:text-orange-500 hover:border-orange-500 hover:bg-orange-500/5' : 'hover:text-green-500 hover:border-green-500 hover:bg-green-500/5' }} transition-all text-[10px] font-black uppercase tracking-widest gap-2">
                                    <span class="material-symbols-outlined text-[18px]">{{ $station->status === 'active' ? 'pause_circle' : 'play_circle' }}</span>
                                    {{ $station->status === 'active' ? 'Halt' : 'Resume' }}
                                </button>
                            </form>

                            <!-- Edit -->
                            <button @click="openEdit({{ $station }})"
                                class="h-12 rounded-2xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary hover:border-primary hover:bg-primary/5 transition-all text-[10px] font-black uppercase tracking-widest gap-2">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                Edit
                            </button>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.stations.destroy', $station) }}" class="col-span-2 w-full mt-2" onsubmit="return confirm('DANGER: Permanent deletion of node registry. Proceed only if node is decommissioned and has no active dependencies.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full h-10 rounded-xl border border-transparent flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-500/5 transition-all text-[9px] font-bold uppercase tracking-[0.2em] gap-2">
                                    <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                                    Decommission Node
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>