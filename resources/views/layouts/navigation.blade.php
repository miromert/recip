<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-orange-600 hover:text-orange-700">
                        Recip
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex items-center">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Recipes
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('recipes.create')" :active="request()->routeIs('recipes.create')">
                            + Add Recipe
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Search -->
                <form action="{{ route('search') }}" method="GET" class="hidden sm:block">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search recipes..."
                        class="rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500 w-48 lg:w-64">
                </form>

                <!-- Unit Toggle -->
                <div x-data="unitToggle()" class="hidden sm:flex items-center">
                    <button @click="toggle()"
                        class="text-xs font-medium px-3 py-1.5 rounded-full border transition-colors"
                        :class="system === 'metric' ? 'bg-orange-100 border-orange-300 text-orange-700' : 'bg-blue-100 border-blue-300 text-blue-700'"
                        :title="system === 'metric' ? 'Switch to imperial' : 'Switch to metric'">
                        <span x-text="system === 'metric' ? 'Metric' : 'Imperial'"></span>
                    </button>
                </div>

                @auth
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('users.show', Auth::user())">My Recipes</x-dropdown-link>
                                <x-dropdown-link :href="route('profile.edit')">Settings</x-dropdown-link>
                                @if(Auth::user()->is_admin)
                                    <x-dropdown-link :href="route('admin.index')">Admin</x-dropdown-link>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        Log Out
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-3 text-sm">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                        <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">Sign up</a>
                    </div>
                @endauth

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="px-4 pt-2">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search recipes..."
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-orange-500 focus:ring-orange-500">
            </form>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">Recipes</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('recipes.create')" :active="request()->routeIs('recipes.create')">+ Add Recipe</x-responsive-nav-link>
            @endauth
        </div>

        <div class="px-4 pb-3" x-data="unitToggle()">
            <button @click="toggle()" class="text-xs font-medium px-3 py-1.5 rounded-full border transition-colors"
                :class="system === 'metric' ? 'bg-orange-100 border-orange-300 text-orange-700' : 'bg-blue-100 border-blue-300 text-blue-700'">
                <span x-text="system === 'metric' ? 'Metric' : 'Imperial'"></span>
            </button>
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('users.show', Auth::user())">My Recipes</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">Settings</x-responsive-nav-link>
                    @if(Auth::user()->is_admin)
                        <x-responsive-nav-link :href="route('admin.index')">Admin</x-responsive-nav-link>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                            Log Out
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-200 px-4 flex gap-3">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 text-sm">Log in</a>
                <a href="{{ route('register') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 text-sm">Sign up</a>
            </div>
        @endauth
    </div>
</nav>
