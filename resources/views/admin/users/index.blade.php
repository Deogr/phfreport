<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Admin</a>
            <span class="mx-2 text-gray-300">/</span>
            <span class="font-medium text-gray-900 dark:text-white">System Users</span>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
        showEditModal: false, 
        editingUser: {},
        openEditModal(user) {
            this.editingUser = user;
            this.showEditModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Page Actions -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Access Control</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Manage system administrators, managers, and
                        operational staff.</p>
                </div>
                <button x-data="" x-on:click="$dispatch('open-modal', 'create-user-modal')"
                    class="flex items-center justify-center h-11 px-6 rounded-md bg-primary text-white text-sm font-bold uppercase tracking-widest hover:bg-primary-hover transition shadow-sm w-full md:w-auto">
                    <span class="material-symbols-outlined text-[20px] mr-2">person_add</span>
                    Register User
                </button>
            </div>

            <!-- Users List -->
            <div
                class="bg-white dark:bg-surface-dark shadow-sm border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/20">
                                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">User Profile
                                </th>
                                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                                <th
                                    class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide hidden sm:table-cell">
                                    Contact Info
                                </th>
                                <th
                                    class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center hidden md:table-cell">
                                    Security Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold overflow-hidden">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=137fec&color=fff"
                                                    alt="">
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase tracking-tighter">ID:
                                                    #{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                        @if($user->role === 'admin') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400
                                                        @elseif($user->role === 'manager') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                                        @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="p-4 hidden sm:table-cell">
                                        <div class="flex flex-col">
                                            <span class="text-gray-900 dark:text-gray-200">{{ $user->email }}</span>
                                            <span class="text-xs text-gray-500">{{ $user->phone ?? 'Unset' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center hidden md:table-cell">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                        @if($user->status === 'active') bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400
                                                        @elseif($user->status === 'suspended') bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400
                                                        @else bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 @endif">
                                            {{ $user->status ?? 'Active' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <!-- Quick Status Toggles -->
                                            @if($user->id !== auth()->id())
                                                @if($user->status === 'active')
                                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="suspended">
                                                        <button title="Suspend User"
                                                            class="size-8 flex items-center justify-center bg-orange-50 text-orange-600 rounded hover:bg-orange-100 transition-colors">
                                                            <span class="material-symbols-outlined text-[18px]">block</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                                        class="inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="active">
                                                        <button title="Activate User"
                                                            class="size-8 flex items-center justify-center bg-green-50 text-green-600 rounded hover:bg-green-100 transition-colors">
                                                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            <!-- Edit/Delete -->
                                            <button @click="openEditModal({{ json_encode($user) }})"
                                                class="size-8 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded text-gray-400 hover:text-primary hover:bg-blue-50 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>

                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                    onsubmit="return confirm('WARNING: Are you sure you want to PERMANENTLY delete this user? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="size-8 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create User Modal -->
            <x-modal name="create-user-modal" focusable>
                <form method="post" action="{{ route('admin.users.store') }}" class="p-6">
                    @csrf
                    <div class="flex items-center gap-3 mb-6">
                        <div class="size-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[28px]">person_add</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 uppercase tracking-tight">
                                Register New User</h2>
                            <p class="text-sm text-gray-500">Provide system access to a new team member.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="role" :value="__('System Role')" />
                            <select name="role" id="role"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="receptionist">Receptionist (Operations)</option>
                                <option value="manager">Manager (Branch/Staff Control)</option>
                                <option value="admin">Administrator (System Wide)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Initial Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                required />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                class="mt-1 block w-full" required />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                        <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                        <x-primary-button>Register Account</x-primary-button>
                    </div>
                </form>
            </x-modal>

            <!-- Edit User Modal (Alpine.js driven) -->
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showEditModal = false">
                        <div class="absolute inset-0 bg-gray-500 opacity-75 dark:bg-gray-900 dark:opacity-90"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white dark:bg-surface-dark rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <form :action="'{{ url('/admin/users') }}/' + editingUser.id" method="POST" class="p-6">
                            @csrf
                            @method('PUT')

                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">
                                    Edit User Profile</h2>
                                <button type="button" @click="showEditModal = false"
                                    class="text-gray-400 hover:text-gray-500">
                                    <span class="material-symbols-outlined">close</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="edit_name" :value="__('Full Name')" />
                                    <x-text-input id="edit_name" name="name" type="text" class="mt-1 block w-full"
                                        x-model="editingUser.name" required />
                                </div>
                                <div>
                                    <x-input-label for="edit_role" :value="__('System Role')" />
                                    <select name="role" id="edit_role" x-model="editingUser.role"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="receptionist">Receptionist</option>
                                        <option value="manager">Manager</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <x-input-label for="edit_email" :value="__('Email')" />
                                            <x-text-input id="edit_email" name="email" type="email"
                                                class="mt-1 block w-full" x-model="editingUser.email" required />
                                        </div>
                                        <div>
                                            <x-input-label for="edit_phone" :value="__('Phone')" />
                                            <x-text-input id="edit_phone" name="phone" type="text"
                                                class="mt-1 block w-full" x-model="editingUser.phone" />
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="edit_status" :value="__('Account Status')" />
                                    <div class="mt-2 grid grid-cols-3 gap-3">
                                        <label
                                            class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none"
                                            :class="editingUser.status === 'active' ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-300 dark:border-gray-700'">
                                            <input type="radio" name="status" value="active"
                                                x-model="editingUser.status" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span
                                                        class="block text-sm font-medium text-gray-900 dark:text-gray-100">Active</span>
                                                    <span class="mt-1 flex items-center text-xs text-gray-500">Full
                                                        Access</span>
                                                </span>
                                            </span>
                                        </label>
                                        <label
                                            class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none"
                                            :class="editingUser.status === 'suspended' ? 'border-orange-500 ring-1 ring-orange-500' : 'border-gray-300 dark:border-gray-700'">
                                            <input type="radio" name="status" value="suspended"
                                                x-model="editingUser.status" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span
                                                        class="block text-sm font-medium text-gray-900 dark:text-gray-100">Suspended</span>
                                                    <span class="mt-1 flex items-center text-xs text-gray-500">Temp
                                                        Block</span>
                                                </span>
                                            </span>
                                        </label>
                                        <label
                                            class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none"
                                            :class="editingUser.status === 'inactive' ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300 dark:border-gray-700'">
                                            <input type="radio" name="status" value="inactive"
                                                x-model="editingUser.status" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span
                                                        class="block text-sm font-medium text-gray-900 dark:text-gray-100">Deactivated</span>
                                                    <span class="mt-1 flex items-center text-xs text-gray-500">No
                                                        Access</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="md:col-span-2 pt-4">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-md">
                                        <x-input-label for="edit_password" :value="__('Change Password (leave blank to keep current)')" />
                                        <x-text-input id="edit_password" name="password" type="password"
                                            class="mt-1 block w-full bg-white dark:bg-gray-800" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
                                <x-secondary-button @click="showEditModal = false">Cancel</x-secondary-button>
                                <x-primary-button>Update User Settings</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>