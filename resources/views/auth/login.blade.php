<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Welcome back</h1>
        <p class="text-sm text-slate-500 mt-1">Sign in to your account to continue</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email" class="mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-brand-600 hover:text-brand-700 font-medium" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="mt-1" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-surface-border text-brand-600 focus:ring-brand-500" name="remember">
            <label for="remember_me" class="ml-2 text-sm text-slate-600">{{ __('Remember me') }}</label>
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Sign in') }}
        </x-primary-button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-700">
            Create account
        </a>
    </p>
</x-guest-layout>
