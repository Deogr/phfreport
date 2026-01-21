<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Admin</a>
            <span class="mx-2 text-gray-300">/</span>
            <span class="font-medium text-gray-900 dark:text-white">User Management</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">

            <!-- Page Heading -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Personnel Management
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Manage receptionists, assign primary stations, and
                        monitor staff status.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button x-data="" x-on:click="$dispatch('open-modal', 'create-user')"
                        class="flex items-center justify-center h-10 px-6 rounded-md bg-primary text-white text-sm font-bold uppercase tracking-widest hover:bg-primary-hover transition shadow-sm shadow-blue-200 dark:shadow-none">
                        <span class="material-symbols-outlined text-[20px] mr-2">person_add</span>
                        Onboard Staff
                    </button>
                </div>
            </div>

            <!-- Create User Modal -->
            <x-modal name="create-user" focusable>
                <form method="post" action="{{ route('admin.receptionists.store') }}" class="p-6">
                    @csrf
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Onboard New Staff Member') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Create a new account for a receptionist.') }}
                    </p>

                    <div class="mt-6">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')"
                            required placeholder="Full Legal Name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            :value="old('email')" required placeholder="staff@example.com" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="phone" :value="__('Phone Number')" />
                        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                            :value="old('phone')" placeholder="+250..." />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="password" :value="__('Default Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                            required />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <div class="mt-6">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full" required />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-secondary-button x-on:click="$dispatch('close')">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button>
                            {{ __('Create Account') }}
                        </x-primary-button>
                    </div>
                </form>
            </x-modal>

            <div
                class="bg-white dark:bg-surface-dark rounded-md shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/20">
                                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Receptionist
                                </th>
                                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact</th>
                                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">
                                    Status</th>
                                <th class="p-4 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">
                                    Joined</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-10 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden shrink-0">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=137fec&color=fff"
                                                    alt="{{ $user->name }}" class="size-full object-cover">
                                            </div>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-bold text-gray-900 dark:text-white leading-tight">{{ $user->name }}</span>
                                                <span
                                                    class="text-xs text-gray-500 font-medium">{{ $user->role ?? 'Receptionist' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-gray-600 dark:text-gray-400 font-medium">
                                        <div class="flex flex-col">
                                            <span>{{ $user->email }}</span>
                                            <span class="text-xs text-gray-400">{{ $user->phone ?? 'No phone' }}</span>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400">
                                            Active
                                        </span>
                                    </td>
                                    <td class="p-4 text-right text-gray-500 text-xs">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <div
                                            class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button
                                                class="size-8 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded text-gray-400 hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>
                                            <form method="POST" action="{{ route('admin.receptionists.destroy', $user) }}"
                                                onsubmit="return confirm('Are you sure you want to remove this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="size-8 flex items-center justify-center bg-gray-100 dark:bg-gray-800 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
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
</x-app-layout>