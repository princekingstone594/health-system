<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
        <p class="text-sm text-slate-500 mt-1">Sign in to your account to continue</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="mail" class="w-4.5 h-4.5" />
                </span>
                <x-text-input id="email" class="w-full pl-10"
                    type="email" name="email"
                    :value="old('email')"
                    required autofocus autocomplete="username"
                    placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-brand-600 hover:text-brand-700 font-medium"
                       href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="lock" class="w-4.5 h-4.5" />
                </span>
                <x-text-input id="password"
                    class="w-full pl-10 pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••" />

                <!-- Toggle Button -->
                <button type="button"
                    onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <x-icon name="eye" class="w-4.5 h-4.5" />
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember -->
        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer select-none">
                <input id="remember_me" type="checkbox"
                    class="rounded border-surface-border text-brand-600 focus:ring-brand-500 cursor-pointer"
                    name="remember">
                <span class="ml-2 text-sm text-slate-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-semibold">
            <x-icon name="arrow-up-right" class="w-4 h-4" />
            {{ __('Sign in') }}
        </button>
    </form>

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-surface-border"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="bg-white px-4 text-slate-400">or continue with</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <button class="btn-secondary justify-center py-2.5">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
        </button>
        <button class="btn-secondary justify-center py-2.5">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 0c-6.627 0-12 5.373-12 12 0 5.302 3.438 9.8 8.205 11.387.6.113.82-.26.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.108-.775.418-1.305.762-1.605-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222 0 1.606-.014 2.899-.014 3.293 0 .319.216.694.824.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
            </svg>
            GitHub
        </button>
    </div>

    <p class="text-center text-sm text-slate-500 mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-700">
            Create account
        </a>
    </p>

    <!-- Toggle Script -->
    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            btn.innerHTML = isPassword ? '<svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>' : '<svg class="w-4.5 h-4.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        }
    </script>
</x-guest-layout>