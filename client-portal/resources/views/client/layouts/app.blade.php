<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Law Firm Portal') }} - Client Portal</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 shadow-xl border-b border-slate-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <div class="bg-blue-600 p-2 rounded-lg mr-3 shadow-lg">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-white text-xl font-bold tracking-tight">Law Firm</h1>
                                <p class="text-blue-200 text-xs font-medium">Client Portal</p>
                            </div>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-1 sm:ml-10 sm:flex">
                            <a href="{{ route('client.dashboard') }}" 
                               class="{{ request()->routeIs('client.dashboard') ? 'bg-blue-700 text-white border-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white border-transparent' }} 
                                      inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition duration-200 rounded-t-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('client.tasks.index') }}" 
                               class="{{ request()->routeIs('client.tasks.*') ? 'bg-blue-700 text-white border-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white border-transparent' }} 
                                      inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition duration-200 rounded-t-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                My Tasks
                            </a>
                            <a href="{{ route('client.documents.index') }}" 
                               class="{{ request()->routeIs('client.documents.*') ? 'bg-blue-700 text-white border-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white border-transparent' }} 
                                      inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition duration-200 rounded-t-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Documents
                            </a>
                            <a href="{{ route('client.calendar.index') }}" 
                               class="{{ request()->routeIs('client.calendar.*') ? 'bg-blue-700 text-white border-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white border-transparent' }} 
                                      inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition duration-200 rounded-t-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Calendar
                            </a>
                            <a href="{{ route('client.chat.index') }}" 
                               class="{{ request()->routeIs('client.chat.*') ? 'bg-blue-700 text-white border-blue-400' : 'text-slate-300 hover:bg-slate-700 hover:text-white border-transparent' }} 
                                      inline-flex items-center px-4 py-2 border-b-2 text-sm font-medium transition duration-200 rounded-t-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Messages
                            </a>
                        </div>
                    </div>

                    <!-- Client Info & Logout -->
                    <div class="hidden sm:flex sm:items-center sm:space-x-4">
                        <div class="flex items-center space-x-3 bg-slate-800 bg-opacity-50 rounded-lg px-4 py-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">{{ substr($currentClient->first_name, 0, 1) }}{{ substr($currentClient->last_name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-white text-sm font-medium">{{ $currentClient->first_name }} {{ $currentClient->last_name }}</p>
                                <p class="text-slate-300 text-xs">Client</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('client.logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button type="button" class="bg-slate-800 inline-flex items-center justify-center p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 focus:outline-none focus:bg-slate-700 focus:text-white transition duration-200 shadow-md" id="mobile-menu-button">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="sm:hidden hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('client.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('client.dashboard') ? 'border-blue-300 text-blue-100 bg-blue-700' : 'border-transparent text-blue-200 hover:text-white hover:bg-blue-700' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('client.tasks.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('client.tasks.*') ? 'border-blue-300 text-blue-100 bg-blue-700' : 'border-transparent text-blue-200 hover:text-white hover:bg-blue-700' }}">
                        My Tasks
                    </a>
                    <a href="{{ route('client.documents.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('client.documents.*') ? 'border-blue-300 text-blue-100 bg-blue-700' : 'border-transparent text-blue-200 hover:text-white hover:bg-blue-700' }}">
                        Documents
                    </a>
                    <a href="{{ route('client.calendar.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('client.calendar.*') ? 'border-blue-300 text-blue-100 bg-blue-700' : 'border-transparent text-blue-200 hover:text-white hover:bg-blue-700' }}">
                        Calendar
                    </a>
                    <a href="{{ route('client.chat.index') }}" class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium {{ request()->routeIs('client.chat.*') ? 'border-blue-300 text-blue-100 bg-blue-700' : 'border-transparent text-blue-200 hover:text-white hover:bg-blue-700' }}">
                        Messages
                    </a>
                </div>
                <div class="pt-4 pb-1 border-t border-blue-700">
                    <div class="px-4">
                        <div class="text-white text-sm">{{ $currentClient->full_name }}</div>
                        <div class="text-blue-200 text-sm">{{ $currentClient->email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('client.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left pl-4 pr-4 py-2 text-base font-medium text-blue-200 hover:text-white hover:bg-blue-700">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-6">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>