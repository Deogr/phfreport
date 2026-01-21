<x-guest-layout>
    <div
        class="group/design-root w-full rounded-xl bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-black/5 dark:bg-[#1A232E] dark:ring-white/10 sm:p-10">
        <!-- Header / Logo -->
        <div class="flex flex-col items-center">
            <div class="flex items-center gap-3 text-primary">
                <div class="flex items-center justify-center">
                    <img src="{{ asset('images/logo.jpg') }}" alt="PHF Report Logo" class="h-12 w-auto object-contain">
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-[#111418] dark:text-white">PHF Systems</h2>
            </div>
            <h1 class="mt-8 text-center text-xl font-bold leading-tight tracking-tight text-[#111418] dark:text-white">
                Log in
                to your account</h1>
            <p class="mt-2 text-center text-sm text-[#617589] dark:text-gray-400">Welcome back! Please enter your
                details.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="mt-10 space-y-6">
            @csrf

            <!-- Email Field -->
            <div>
                <label class="mb-2 block text-sm font-medium leading-normal text-[#111418] dark:text-gray-200">
                    Email
                </label>
                <input
                    class="form-input block h-12 w-full rounded-lg border border-[#dbe0e6] bg-white p-[15px] text-base text-[#111418] placeholder-[#617589] focus:border-primary focus:outline-0 focus:ring-0 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500 dark:focus:border-primary"
                    placeholder="name@company.com" type="email" name="email" value="{{ old('email') }}" required
                    autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password Field -->
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-medium leading-normal text-[#111418] dark:text-gray-200">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-primary hover:text-primary-hover hover:underline"
                            href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <div
                    class="relative flex w-full items-center rounded-lg border border-[#dbe0e6] bg-white dark:border-gray-700 dark:bg-gray-800 focus-within:border-primary dark:focus-within:border-primary">
                    <input
                        class="form-input flex h-12 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-r-none border-0 bg-transparent p-[15px] text-base text-[#111418] placeholder-[#617589] focus:outline-0 focus:ring-0 dark:text-white dark:placeholder-gray-500"
                        placeholder="••••••••" type="password" name="password" required
                        autocomplete="current-password" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary shadow-sm focus:ring-primary dark:focus:ring-primary dark:focus:ring-offset-gray-800"
                        name="remember">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                class="flex h-12 w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-primary px-5 text-base font-bold leading-normal tracking-[0.015em] text-white transition-colors duration-200 hover:bg-primary-hover focus:outline-none focus:ring-4 focus:ring-primary/20">
                <span class="truncate">Sign In</span>
            </button>
        </form>
    </div>

    <!-- Footer -->
    <div class="mt-8 flex flex-col items-center justify-center gap-4">
        <p class="text-xs text-[#617589] dark:text-gray-500">
            © 2024 PHF Systems. All rights reserved.
        </p>
        <div class="flex items-center gap-4 text-xs text-[#617589] dark:text-gray-500">
            <a class="hover:text-[#111418] dark:hover:text-gray-300" href="#">Privacy Policy</a>
            <span>·</span>
            <a class="hover:text-[#111418] dark:hover:text-gray-300" href="#">Terms of Service</a>
        </div>
    </div>
</x-guest-layout>