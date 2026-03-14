<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lead Management') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">

        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200" x-data="{ mobileMenu: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">

                    <!-- LEFT SIDE -->
                    <div class="flex">

                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="flex items-center">
                                <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="ml-2 text-xl font-bold text-gray-900 hidden sm:block">Lead
                                    Management</span>
                            </a>
                        </div>

                        <!-- Desktop Menu -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">

                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Dashboard
                            </a>

                            <a href="{{ route('leads.index') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('leads.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Leads
                            </a>

                            <a href="{{ route('orders.index') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('orders.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Orders
                            </a>

                            <a href="{{ route('repurchase.index') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('repurchase.index') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Repurchase
                            </a>

                            <a href="{{ route('followups.dashboard') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('followups.dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Follow Up
                            </a>

                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="flex items-center gap-3">

                        <!-- Mobile hamburger -->
                        <div class="sm:hidden">
                            <button @click="mobileMenu=!mobileMenu"
                                class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">

                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />

                                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />

                                </svg>
                            </button>
                        </div>

                        @livewire('organization-switcher')

                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">

                            <button @click="open=!open"
                                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">

                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>

                                <span class="hidden md:inline">{{ auth()->user()->name }}</span>

                                <svg class="w-4 h-4" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                            </button>

                            <div x-show="open" @click.away="open=false"
                                class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5"
                                style="display:none;">

                                <div class="py-1">

                                    @if (Route::has('profile.edit'))
                                        <a href="{{ route('profile.edit') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Profile
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>

                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('organizations.index') }}"
                                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Organization Setting
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- MOBILE MENU -->
            <div x-show="mobileMenu" class="sm:hidden border-t border-gray-200">

                <div class="pt-2 pb-3 space-y-1">

                    <a href="{{ route('dashboard') }}"
                        class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                        Dashboard
                    </a>

                    <a href="{{ route('leads.index') }}"
                        class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                        Leads
                    </a>

                    <a href="{{ route('orders.index') }}"
                        class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                        Orders
                    </a>

                    <a href="{{ route('repurchase.index') }}"
                        class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                        Repurchase
                    </a>

                    <a href="{{ route('followups.dashboard') }}"
                        class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:bg-gray-50">
                        Follow Up
                    </a>

                </div>
            </div>

        </nav>

        <main>
            {{ $slot }}
        </main>

    </div>

    @livewireScripts
</body>

</html>

