<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    
    <!-- Primary Navigation -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- LEFT -->
            <div class="flex">
                
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="
                        @auth
                            @if(auth()->user()->role === 'doctor')
                                {{ route('doctor.dashboard') }}
                            @elseif(auth()->user()->role === 'patient')
                                {{ route('patient.dashboard') }}
                            @elseif(auth()->user()->role === 'receptionist')
                                {{ route('receptionist.dashboard') }}
                            @else
                                {{ route('dashboard') }}
                            @endif
                        @else
                            {{ route('dashboard') }}
                        @endauth
                    ">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Desktop Nav -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                    @auth

                        <!-- ROLE DASHBOARD -->
                        @if(auth()->user()->role === 'doctor')
                            <x-nav-link :href="route('doctor.dashboard')" :active="request()->routeIs('doctor.dashboard')">
                                Doctor Dashboard
                            </x-nav-link>
                        @elseif(auth()->user()->role === 'patient')
                            <x-nav-link :href="route('patient.dashboard')" :active="request()->routeIs('patient.dashboard')">
                                My Dashboard
                            </x-nav-link>
                        @elseif(auth()->user()->role === 'receptionist')
                            <x-nav-link :href="route('receptionist.dashboard')" :active="request()->routeIs('receptionist.dashboard')">
                                Reception
                            </x-nav-link>
                        @endif

                        <!-- PATIENTS (Receptionist) -->
                        @if(in_array(auth()->user()->role, ['admin', 'receptionist']))
                            <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                                Patients
                            </x-nav-link>
                        @endif

                        <!-- APPOINTMENTS -->
                        @if(in_array(auth()->user()->role, ['doctor', 'receptionist']))
                            <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
                                Appointments
                            </x-nav-link>
                        @endif

                        <!-- PATIENT APPOINTMENTS -->
                        @if(auth()->user()->role === 'patient')
                            <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
                                My Appointments
                            </x-nav-link>
                        @endif

                        <!-- DOCTOR ONLY -->
                        @if(auth()->user()->role === 'doctor')
                            <x-nav-link :href="route('availability.index')" :active="request()->routeIs('availability.*')">
                                Availability
                            </x-nav-link>

                            <x-nav-link :href="route('availability.calendar')" :active="request()->routeIs('availability.calendar')">
                                Calendar
                            </x-nav-link>
                        @endif

                        <!-- PATIENT ONLY -->
                        @if(auth()->user()->role === 'patient')
                            <x-nav-link :href="route('patient.history')" :active="request()->routeIs('patient.history')">
                                My History
                            </x-nav-link>

                            <x-nav-link :href="route('ai.chat')" :active="request()->routeIs('ai.chat')">
                                AI Chat
                            </x-nav-link>
                        @endif

                    @endauth

                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            
                            <div>
                                {{ Auth::user()->name }} 
                                <span class="text-xs text-gray-400">({{ Auth::user()->role }})</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profile
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>

                </x-dropdown>
                @endauth
            </div>

            <!-- MOBILE TOGGLE -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <div class="pt-2 pb-3 space-y-1">

            @auth

                <!-- DASHBOARD -->
                @if(auth()->user()->role === 'doctor')
                    <x-responsive-nav-link :href="route('doctor.dashboard')">
                        Doctor Dashboard
                    </x-responsive-nav-link>
                @elseif(auth()->user()->role === 'patient')
                    <x-responsive-nav-link :href="route('patient.dashboard')">
                        My Dashboard
                    </x-responsive-nav-link>
                @elseif(auth()->user()->role === 'receptionist')
                    <x-responsive-nav-link :href="route('receptionist.dashboard')">
                        Reception
                    </x-responsive-nav-link>
                @endif

                <!-- SHARED -->
                @if(in_array(auth()->user()->role, ['doctor', 'receptionist']))
                    <x-responsive-nav-link :href="route('appointments.index')">
                        Appointments
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'patient')
                    <x-responsive-nav-link :href="route('appointments.index')">
                        My Appointments
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('patient.history')">
                        My History
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('ai.chat')">
                        AI Chat
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'doctor')
                    <x-responsive-nav-link :href="route('availability.index')">
                        Availability
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('availability.calendar')">
                        Calendar
                    </x-responsive-nav-link>
                @endif

                @if(auth()->user()->role === 'receptionist')
                    <x-responsive-nav-link :href="route('patients.index')">
                        Patients
                    </x-responsive-nav-link>
                @endif

            @endauth

        </div>

        <!-- USER INFO -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                    {{ Auth::user()->name }}
                </div>

                <div class="text-sm text-gray-500">
                    {{ Auth::user()->email }}
                </div>

                <div class="text-xs text-gray-400">
                    Role: {{ Auth::user()->role }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profile
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>

            </div>
        </div>
    </div>
</nav>