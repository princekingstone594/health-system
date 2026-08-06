<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur border-b border-gray-200 shadow-sm sticky top-0 z-50">
    
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <!-- LEFT -->
            <div class="flex items-center gap-6">
                
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-8 w-auto text-indigo-600" />
                    <span class="font-semibold text-gray-800 text-lg">MediSys</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden sm:flex items-center gap-2">

                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition
                       {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                        Dashboard
                    </a>

                    <!-- Patients -->
                    @if(in_array(auth()->user()->role, ['admin', 'receptionist']))
                        <a href="{{ route('patients.index') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition
                           {{ request()->routeIs('patients.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            Patients
                        </a>
                    @endif

                    <!-- Appointments -->
                    @if(in_array(auth()->user()->role, ['admin', 'doctor', 'receptionist']))
                        <a href="{{ route('appointments.create') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition
                           {{ request()->routeIs('appointments.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            Appointments
                        </a>
                    @endif

                    <!-- Doctor -->
                    @if(auth()->user()->role === 'doctor')
                        <a href="{{ route('availability.index') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition
                           {{ request()->routeIs('availability.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            Availability
                        </a>

                        <a href="{{ route('availability.calendar') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition
                           {{ request()->routeIs('availability.calendar') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            Calendar
                        </a>
                    @endif

                    <!-- Patient -->
                    @if(auth()->user()->role === 'patient')
                        <a href="{{ route('patient.history') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition
                           {{ request()->routeIs('patient.history') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-100' }}">
                            My History
                        </a>
                    @endif

                </div>
            </div>

            <!-- RIGHT -->
            <div class="hidden sm:flex items-center gap-4">

                <!-- Role Badge -->
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600 capitalize">
                    {{ Auth::user()->role }}
                </span>

                <!-- User Dropdown -->
                <div class="relative">
                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition">

                        <div class="text-sm text-gray-700">
                            {{ Auth::user()->name }}
                        </div>

                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border py-2">

                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MOBILE -->
            <div class="sm:hidden flex items-center">
                <button @click="open = ! open"
                        class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor">
                        <path :class="{'hidden': open, 'block': !open}" stroke-linecap="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'block': open, 'hidden': !open}" stroke-linecap="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div x-show="open" x-transition class="sm:hidden bg-white border-t px-4 pb-4 space-y-2">

        <a href="{{ route('dashboard') }}" class="block py-2 text-gray-700">Dashboard</a>

        @if(in_array(auth()->user()->role, ['admin', 'receptionist']))
            <a href="{{ route('patients.index') }}" class="block py-2 text-gray-700">Patients</a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'doctor', 'receptionist']))
            <a href="{{ route('appointments.create') }}" class="block py-2 text-gray-700">Appointments</a>
        @endif

        @if(auth()->user()->role === 'doctor')
            <a href="{{ route('availability.index') }}" class="block py-2 text-gray-700">Availability</a>
            <a href="{{ route('availability.calendar') }}" class="block py-2 text-gray-700">Calendar</a>
        @endif

        @if(auth()->user()->role === 'patient')
            <a href="{{ route('patient.history') }}" class="block py-2 text-gray-700">My History</a>
        @endif

        <hr>

        <a href="{{ route('profile.edit') }}" class="block py-2 text-gray-600">Profile</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left py-2 text-red-500">Logout</button>
        </form>
    </div>

</nav>