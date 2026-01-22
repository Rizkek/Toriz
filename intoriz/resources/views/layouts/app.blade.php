<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Toriz Inventory</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #f9fafb;
            color: #1f2937;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Light Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Navigation Links */
        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            margin-bottom: 2px;
            border-radius: 6px;
            color: #6b7280;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.15s;
        }

        .nav-link:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .nav-link.active {
            background: #0f766e;
            color: #ffffff;
        }

        .nav-link .material-icons {
            margin-right: 12px;
            font-size: 20px;
        }
    </style>
</head>


<body x-data="{ sidebarOpen: false }" class="bg-gray-50 text-gray-900 min-h-screen">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 md:hidden" x-cloak>
        </div>

        <!-- Left Sidebar Navigation (240px width) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-60 transform transition-transform duration-300 ease-in-out md:static md:translate-x-0 flex flex-col bg-white border-r border-gray-200">

            <!-- Logo (64px height) -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center">
                        <span class="material-icons text-white text-lg">inventory</span>
                    </div>
                    <span class="text-lg font-bold text-gray-900">Toriz</span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden p-1 text-gray-500 hover:text-gray-700">
                    <span class="material-icons text-lg">close</span>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-0.5 overflow-y-auto custom-scrollbar">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-icons">dashboard</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('products.index') }}"
                    class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span class="material-icons">inventory_2</span>
                    <span>Products</span>
                </a>

                <a href="{{ route('stock.transactions') }}"
                    class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                    <span class="material-icons">swap_horiz</span>
                    <span>Transactions</span>
                </a>

                <div class="my-3 border-t border-gray-200"></div>

                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reports</div>

                <a href="{{ route('reports.stock-value') }}"
                    class="nav-link {{ request()->routeIs('reports.stock-value') ? 'active' : '' }}">
                    <span class="material-icons">attach_money</span>
                    <span>Stock Value</span>
                </a>

                <a href="{{ route('reports.low-stock') }}"
                    class="nav-link {{ request()->routeIs('reports.low-stock') ? 'active' : '' }}">
                    <span class="material-icons">warning</span>
                    <span>Low Stock</span>
                </a>

                <a href="{{ route('reports.stock-movement') }}"
                    class="nav-link {{ request()->routeIs('reports.stock-movement') ? 'active' : '' }}">
                    <span class="material-icons">trending_up</span>
                    <span>Movement</span>
                </a>

                <a href="{{ route('reports.expiry') }}"
                    class="nav-link {{ request()->routeIs('reports.expiry') ? 'active' : '' }}">
                    <span class="material-icons">event_busy</span>
                    <span>Expiry</span>
                </a>
            </nav>

            <!-- User Profile Card -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-linear-to-br from-orange-400 to-orange-600 flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-white">A</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">Admin</p>
                        <p class="text-xs text-gray-500 truncate">admin@toriz.com</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar (64px height) -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 shadow-sm">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = true"
                        class="md:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                        <span class="material-icons">menu</span>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-900">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <input type="text" placeholder="Search products..."
                                class="w-56 bg-gray-50 border border-gray-200 rounded-lg px-4 py-1.5 text-sm placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-teal-500 focus:border-transparent transition-all">
                            <span class="material-icons absolute right-3 top-1.5 text-gray-400 text-lg">search</span>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <span class="material-icons">notifications</span>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    </button>

                    <!-- Profile -->
                    <div class="w-9 h-9 rounded-full bg-linear-to-br from-teal-400 to-teal-600 flex items-center justify-center cursor-pointer hover:shadow-md transition-shadow">
                        <span class="material-icons text-sm text-white">person</span>
                    </div>
                </div>
            </header>

            <!-- Main Content Scroll Area -->
            <main class="flex-1 overflow-y-auto px-4 py-6 sm:px-6 custom-scrollbar">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
