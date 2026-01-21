<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Admin</a>
            <span class="mx-2 text-gray-300">/</span>
            <span class="font-medium text-gray-900 dark:text-white">Service Management</span>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        editMode: false,
        currentService: null,
        editName: '',
        editPrice: '',
        editDescription: '',
        editStatus: '',
        editUrl: '',

        openEdit(service) {
            this.currentService = service;
            this.editName = service.name;
            this.editPrice = service.price;
            this.editDescription = service.description || '';
            this.editStatus = service.status;
            this.editUrl = '{{ route('admin.services.update', ':id') }}'.replace(':id', service.id);
            this.editMode = true;
            this.$dispatch('open-modal', 'edit-service');
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Page Heading -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">
                        SERVICE CATALOG
                    </h2>
                    <p class="text-sm font-bold text-gray-400 dark:text-gray-500 mt-1 uppercase tracking-[0.2em]">Manage Fees & Offerings</p>
                </div>
                <div class="flex items-center gap-3">
                    <button x-on:click="$dispatch('open-modal', 'create-service')"
                        class="group flex items-center justify-center h-12 px-8 rounded-xl bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] hover:bg-primary-hover transition-all shadow-xl shadow-primary/20">
                        <span class="material-symbols-outlined text-[20px] mr-2 group-hover:rotate-90 transition-transform">add_circle</span>
                        Add New Service
                    </button>
                </div>
            </div>

            <x-modal name="create-service" focusable>
                <form method="post" action="{{ route('admin.services.store') }}" class="p-8">
                    @csrf
                    <div class="flex items-center gap-4 mb-8">
                        <div class="size-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">spa</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                                {{ __('NEW OFFERING') }}
                            </h2>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Service definition</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" :value="__('Service Name')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="name" name="name" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800" :value="old('name')"
                                required placeholder="e.g. Gym Access, Sauna" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="price" :value="__('Base Price (RWF)')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="price" name="price" type="number" min="0" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                :value="old('price')" required placeholder="0" />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="description" name="description" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                :value="old('description')" placeholder="Brief service summary" />
                        </div>

                        <div>
                            <x-input-label for="status" :value="__('Operational Status')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <select id="status" name="status"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                <option value="active">Active / Available</option>
                                <option value="inactive">Inactive / Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end gap-3 border-t border-gray-50 dark:border-gray-800 pt-6">
                        <x-secondary-button x-on:click="$dispatch('close')" class="text-[10px] font-black uppercase tracking-widest h-11 px-8">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button class="h-11 px-8 text-[10px] font-black uppercase tracking-widest">
                            {{ __('Create Service') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-modal>

            <!-- Edit Service Modal -->
            <x-modal name="edit-service" x-show="editMode" @close="editMode = false" focusable>
                <form :action="editUrl" method="post" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4 mb-8">
                        <div class="size-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">edit_note</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">
                                {{ __('MODIFY SERVICE') }}
                            </h2>
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">ID: <span x-text="currentService?.id"></span> Configuration</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="edit_name" :value="__('Service Name')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="edit_name" name="name" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                x-model="editName" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="edit_price" :value="__('Base Price (RWF)')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="edit_price" name="price" type="number" min="0" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                x-model="editPrice" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price')" />
                        </div>

                        <div>
                            <x-input-label for="edit_description" :value="__('Description')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <x-text-input id="edit_description" name="description" type="text" class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800"
                                x-model="editDescription" placeholder="Brief service summary" />
                        </div>

                         <div>
                            <x-input-label for="edit_status" :value="__('Operational Status')" class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2" />
                            <select id="edit_status" name="status" x-model="editStatus"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-800 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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

            <!-- Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
                @foreach($services as $service)
                    <div class="bg-white dark:bg-surface-dark p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/40 dark:shadow-none flex flex-col gap-8 transition-all hover:scale-[1.03] group relative overflow-hidden">

                        <!-- Status Indicator -->
                        <div class="flex justify-between items-start">
                            <div class="size-14 rounded-2xl bg-primary/5 dark:bg-white/5 flex items-center justify-center text-primary border border-primary/10 group-hover:bg-primary group-hover:text-white transition-all duration-500 shadow-lg shadow-primary/5">
                                <span class="material-symbols-outlined text-[32px]">spa</span>
                            </div>
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $service->status === 'active' ? 'bg-green-50 text-green-700 ring-1 ring-green-100 dark:bg-green-900/20 dark:text-green-400 dark:ring-0' : 'bg-gray-50 text-gray-700 ring-1 ring-gray-100 dark:bg-gray-900/20 dark:text-gray-400 dark:ring-0' }}">
                                <span class="size-2 rounded-full mr-2 {{ $service->status === 'active' ? 'bg-green-500 animate-pulse' : 'bg-gray-500' }}"></span>
                                {{ $service->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter leading-tight group-hover:text-primary transition-colors">
                                {{ $service->name }}
                            </h3>
                             <p class="text-xl font-bold text-gray-500 dark:text-gray-400 mt-2">
                                RWF {{ number_format($service->price) }}
                            </p>
                        </div>

                        <div class="mt-auto grid grid-cols-2 gap-4 pt-6 border-t border-gray-50 dark:border-gray-800/50">
                            <!-- Toggle Status -->
                            <form method="POST" action="{{ route('admin.services.toggle-status', $service) }}" class="w-full">
                                @csrf
                                <input type="hidden" name="status" value="{{ $service->status === 'active' ? 'inactive' : 'active' }}">
                                <button type="submit"
                                    class="w-full h-12 rounded-2xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 {{ $service->status === 'active' ? 'hover:text-orange-500 hover:border-orange-500 hover:bg-orange-500/5' : 'hover:text-green-500 hover:border-green-500 hover:bg-green-500/5' }} transition-all text-[10px] font-black uppercase tracking-widest gap-2">
                                    <span class="material-symbols-outlined text-[18px]">{{ $service->status === 'active' ? 'visibility_off' : 'visibility' }}</span>
                                    {{ $service->status === 'active' ? 'Disable' : 'Enable' }}
                                </button>
                            </form>

                            <!-- Edit -->
                            <button @click="openEdit({{ $service }})"
                                class="h-12 rounded-2xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary hover:border-primary hover:bg-primary/5 transition-all text-[10px] font-black uppercase tracking-widest gap-2">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                Edit
                            </button>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="col-span-2 w-full mt-2" onsubmit="return confirm('CRITICAL: Permanent deletion of service catalog item. This will not affect historical records but will prevent future selections.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full h-10 rounded-xl border border-transparent flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-500/5 transition-all text-[9px] font-bold uppercase tracking-[0.2em] gap-2">
                                    <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                                    Purge Service
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
