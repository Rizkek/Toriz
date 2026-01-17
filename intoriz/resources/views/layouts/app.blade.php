<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Toriz Inventory</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Navigation Links */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-link.active {
            background: linear-gradient(to right, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.1), 0 2px 4px -1px rgba(99, 102, 241, 0.06);
        }

        .nav-link .material-icons {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        /* Light Mode Navigation */
        .light .nav-link {
            color: #64748b;
        }

        .light .nav-link:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .light .nav-link.active {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
        }
    </style>
</head>


<body x-data="{ 
        sidebarOpen: false,
        darkMode: localStorage.getItem('theme') === 'dark' || !localStorage.getItem('theme')
    }" x-init="$watch('darkMode', value => localStorage.setItem('theme', value ? 'dark' : 'light'))"
    :class="darkMode ? 'dark bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900' : 'bg-gray-50'"
    class="text-gray-100 min-h-screen">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40 md:hidden" x-cloak>
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out md:static md:translate-x-0 flex flex-col"
            :class="darkMode ? 'bg-slate-900/95 backdrop-blur-xl border-r border-slate-700/50 md:bg-slate-900/50' : 'bg-white border-r border-gray-200 shadow-xl'">

            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6"
                :class="darkMode ? 'border-b border-slate-700/50' : 'border-b border-gray-200'">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <span class="material-icons text-white">inventory</span>
                    </div>
                    <span
                        class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                        Toriz
                    </span>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button @click="sidebarOpen = false" class="md:hidden"
                    :class="darkMode ? 'text-gray-400 hover:text-white' : 'text-gray-600 hover:text-gray-900'">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
                <a href="{{ route('dashboard') }}"
                    class="nav-link group {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('dashboard') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('products.index') }}"
                    class="nav-link group {{ request()->routeIs('products.*') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('products.*') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">inventory_2</span>
                    <span>Products</span>
                </a>

                <a href="{{ route('stock.transactions') }}"
                    class="nav-link group {{ request()->routeIs('stock.*') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('stock.*') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">swap_horiz</span>
                    <span>Transactions</span>
                </a>

                <div class="my-2" :class="darkMode ? 'border-t border-slate-700/50' : 'border-t border-gray-200'"></div>

                <a href="{{ route('reports.stock-value') }}"
                    class="nav-link group {{ request()->routeIs('reports.stock-value') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('reports.stock-value') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">attach_money</span>
                    <span>Stock Value</span>
                </a>

                <a href="{{ route('reports.low-stock') }}"
                    class="nav-link group {{ request()->routeIs('reports.low-stock') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('reports.low-stock') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">warning</span>
                    <span>Low Stock</span>
                </a>

                <a href="{{ route('reports.stock-movement') }}"
                    class="nav-link group {{ request()->routeIs('reports.stock-movement') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('reports.stock-movement') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">timeline</span>
                    <span>Movement</span>
                </a>

                <a href="{{ route('reports.expiry') }}"
                    class="nav-link group {{ request()->routeIs('reports.expiry') ? 'active' : '' }}"
                    :class="darkMode ? '' : (!{{ request()->routeIs('reports.expiry') ? 'true' : 'false' }} && 'text-gray-600 hover:bg-gray-100')">
                    <span class="material-icons group-hover:scale-110 transition-transform">event_busy</span>
                    <span>Expiry</span>
                </a>
            </nav>

            <!-- User Profile -->
            <div class="p-4"
                :class="darkMode ? 'border-t border-slate-700/50 bg-slate-800/20' : 'border-t border-gray-200 bg-gray-50'">
                <div
                    class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-700/50 transition-colors cursor-pointer group">
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 to-orange-500 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <span class="text-sm font-bold text-white">A</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">Admin</p>
                        <p class="text-xs text-gray-400 truncate">admin@toriz.com</p>
                    </div>
                    <span class="material-icons text-gray-500 text-sm">settings</span>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Top Bar -->
            <header
                class="h-16 bg-slate-900/30 backdrop-blur-xl border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true"
                        class="md:hidden p-2 -ml-2 mr-2 rounded-lg text-gray-400 hover:text-white hover:bg-slate-700/50">
                        <span class="material-icons">menu</span>
                    </button>
                    <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Theme Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg transition-all"
                        :class="darkMode ? 'bg-slate-800 text-yellow-400 hover:bg-slate-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        title="Toggle theme">
                        <span class="material-icons text-xl" x-show="darkMode">light_mode</span>
                        <span class="material-icons text-xl" x-show="!darkMode" x-cloak>dark_mode</span>
                    </button>

                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <input type="text" placeholder="Search..."
                                class="w-64 bg-slate-800/50 border border-slate-700/50 rounded-xl px-4 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 text-sm">
                            <span class="material-icons absolute right-3 top-1.5 text-gray-400 text-sm">search</span>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen"
                            class="relative p-2 text-gray-400 hover:text-white transition-colors">
                            <span class="material-icons">notifications</span>
                            @if(isset($notifications) && $notifications['count'] > 0)
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                            @endif
                        </button>
                    </div>

                    <!-- Profile -->
                    <div
                        class="w-8 h-8 rounded-full bg-slate-700/50 border border-slate-600/50 flex items-center justify-center">
                        <span class="material-icons text-sm text-gray-300">person</span>
                    </div>
                </div>
            </header>

            <!-- Content Scroll Area -->
            <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 custom-scrollbar">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>