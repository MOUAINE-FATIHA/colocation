<nav x-data="{ open: false }" class="bg-orange-500 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="text-white font-bold text-xl tracking-wide">
                    EasyColoc
                </a>

                <!-- Desktop Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-6">
                    <a href="{{ route('dashboard') }}"
                        class="text-white text-sm font-medium hover:text-orange-200 transition
                        {{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : '' }}">
                        Dashboard
                    </a>
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-white text-sm font-medium hover:text-orange-200 transition
                            {{ request()->routeIs('admin.*') ? 'border-b-2 border-white' : '' }}">
                            Admin
                        </a>
                    @endif
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-white hover:text-orange-200 transition">
                            {{ Auth::user()->name }}
                            <svg class="ms-1 h-4 w-4 fill-current" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Déconnexion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="text-white p-2 rounded-md hover:bg-orange-600 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-orange-600">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" class="block text-white text-sm py-2 hover:text-orange-200">Dashboard</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="block text-white text-sm py-2 hover:text-orange-200">Admin</a>
            @endif
        </div>
        <div class="pt-4 pb-3 border-t border-orange-400 px-4">
            <p class="text-white font-medium">{{ Auth::user()->name }}</p>
            <p class="text-orange-200 text-sm">{{ Auth::user()->email }}</p>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block text-white text-sm py-2 hover:text-orange-200">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block text-white text-sm py-2 hover:text-orange-200">Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</nav>