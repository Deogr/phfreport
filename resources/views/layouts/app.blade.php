<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA Settings -->
    <meta name="theme-color" content="#175cd3">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="PHF Gym">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-[#1f2937] dark:text-gray-100 font-display antialiased overflow-hidden"
    x-data="{ sidebarOpen: false }">

    <div class="flex h-screen w-full overflow-hidden relative">

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" x-cloak x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-20 lg:hidden"
            @click="sidebarOpen = false">
        </div>

        <!-- Sidebar -->
        <aside :class="{ '!translate-x-0': sidebarOpen }"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-surface-dark border-r border-gray-200 dark:border-gray-800 flex flex-col shrink-0 transition-transform duration-300 -translate-x-full lg:static lg:translate-x-0">
            <div class="p-6 flex items-center gap-3">
                <div class="size-8 bg-primary rounded-md flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-xl">fitness_center</span>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-tight text-[#111418] dark:text-white leading-none">PHF Systems
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ Auth::user()->role_label ?? 'Dashboard' }}
                    </p>
                </div>
                <!-- Close Button Mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden ml-auto text-gray-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-2 flex flex-col gap-1">
                @include('layouts.navigation')
            </nav>

            <div class="px-4 pb-2">
                <button id="install-button" style="display: none;"
                    class="flex w-full items-center gap-3 px-3 py-2 rounded-md text-primary bg-primary/10 hover:bg-primary/20 transition-colors cursor-pointer">
                    <span class="material-symbols-outlined">download_for_offline</span>
                    <span class="text-sm font-bold uppercase tracking-wider">Download App</span>
                </button>
            </div>

            <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 px-3 py-2 rounded-md text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition-colors relative z-10 cursor-pointer">
                        <span class="material-symbols-outlined">logout</span>
                        <span class="text-sm font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full overflow-hidden relative w-full">
            <!-- Top Header -->
            <header
                class="h-16 bg-white dark:bg-surface-dark border-b border-[#e5e7eb] dark:border-gray-800 flex items-center justify-between px-6 shrink-0 z-10 relative">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-white/5 rounded-md transition-colors">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <!-- Breadcrumbs -->
                    <div class="flex items-center text-sm font-medium text-slate-500 dark:text-slate-400">
                        @isset($header)
                            {{ $header }}
                        @else
                            <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
                            <span class="mx-2 text-slate-300 dark:text-slate-600">/</span>
                            <span class="text-slate-900 dark:text-white">Overview</span>
                        @endisset
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- System Online Badge -->
                    <div
                        class="hidden sm:flex items-center gap-2 bg-white dark:bg-white/5 px-3 py-1.5 rounded border border-slate-200 dark:border-white/10 text-xs text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="font-medium">System Online</span>
                    </div>

                    <div class="h-8 w-[1px] bg-slate-200 dark:bg-white/10 mx-1"></div>

                    <div
                        class="flex items-center gap-3 pl-1 pr-2 py-1 rounded-full hover:bg-slate-50 dark:hover:bg-white/5 transition-colors cursor-pointer">
                        <div class="hidden md:flex flex-col items-end mr-1">
                            <span
                                class="text-sm font-bold text-slate-900 dark:text-white leading-none">{{ Auth::user()->name }}</span>
                            <span
                                class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">{{ Auth::user()->role }}</span>
                        </div>
                        <div
                            class="bg-primary/10 text-primary size-9 rounded-full flex items-center justify-center font-bold text-xs ring-2 ring-primary/20 overflow-hidden">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Dashboard Content -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-10 scroll-smooth">
                <div class="max-w-7xl mx-auto flex flex-col gap-6 lg:gap-8">
                    {{ $slot }}
                </div>
                <!-- Footer -->
                <footer
                    class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800 text-center text-xs text-gray-400">
                    © {{ date('Y') }} PHF Gym Management Systems. All rights reserved.
                </footer>
            </div>
        </main>
    </div>
    @stack('scripts')

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registered', reg))
                    .catch(err => console.log('Service Worker registration failed', err));
            });
        }

        // Custom Install Prompt logic
        let deferredPrompt;
        const installBtn = document.getElementById('install-button');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPrompt = e;
            // Show the install button
            if (installBtn) {
                installBtn.style.display = 'flex';
                console.log('PWA: Install button shown');
            }
            console.log('PWA: beforeinstallprompt event fired');
        });

        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    // Show the install prompt
                    deferredPrompt.prompt();
                    // Wait for the user to respond to the prompt
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log(`User response to the install prompt: ${outcome}`);
                    // We've used the prompt, and can't use it again, throw it away
                    deferredPrompt = null;
                    // Hide the install button
                    installBtn.style.display = 'none';
                }
            });
        }

        window.addEventListener('appinstalled', (evt) => {
            console.log('PHF Systems was installed.');
            if (installBtn) {
                installBtn.style.display = 'none';
            }
        });
    </script>
    <x-confirm-modal />
</body>

</html>