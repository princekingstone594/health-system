<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Create your account</h1>
        <p class="text-sm text-slate-500 mt-1">Join MedFlow and start your healthcare journey</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="user" class="w-4 h-4" />
                </span>
                <x-text-input id="name"
                    class="block w-full pl-10"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required autofocus autocomplete="name"
                    placeholder="John Doe" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="mail" class="w-4 h-4" />
                </span>
                <x-text-input id="email"
                    class="block w-full pl-10"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required autocomplete="username"
                    placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div>
            <x-input-label for="role" :value="__('Register As')" />
            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="users" class="w-4 h-4" />
                </span>
                <select name="role" id="role"
                    class="form-input pl-10 cursor-pointer">
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="receptionist">Receptionist</option>
                </select>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="lock" class="w-4 h-4" />
                </span>
                <x-text-input id="password"
                    class="block w-full pl-10 pr-10"
                    type="password"
                    name="password"
                    required autocomplete="new-password"
                    placeholder="••••••••" />
                <button type="button"
                    onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <x-icon name="eye" class="w-4 h-4" />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1.5">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="lock" class="w-4 h-4" />
                </span>
                <x-text-input id="password_confirmation"
                    class="block w-full pl-10 pr-10"
                    type="password"
                    name="password_confirmation"
                    required autocomplete="new-password"
                    placeholder="••••••••" />
                <button type="button"
                    onclick="togglePassword('password_confirmation', this)"
                    class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <x-icon name="eye" class="w-4 h-4" />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Actions -->
        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-semibold">
            <x-icon name="check" class="w-4 h-4" />
            {{ __('Create account') }}
        </button>
    </form>

    <p class="text-center text-sm text-slate-500 mt-6">
        {{ __('Already registered?') }}
        <a class="font-medium text-brand-600 hover:text-brand-700"
           href="{{ route('login') }}">
            {{ __('Sign in') }}
        </a>
    </p>

    <!-- Toggle Script -->
    <script>
        function togglePassword(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";
            btn.innerHTML = isPassword ? '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>' : '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        }
    </script>
</x-guest-layout>