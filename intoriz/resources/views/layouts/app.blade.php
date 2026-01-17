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
        body { font-family: 'Inter', sans-serif; }
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 text-gray-100 min-h-screen">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-40 md:hidden"
             x-cloak>
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900/95 backdrop-blur-xl border-r border-slate-700/50 transform transition-transform duration-300 ease-in-out md:static md:translate-x-0 md:bg-slate-900/50 flex flex-col">
            
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-slate-700/50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <span class="material-icons text-white">inventory</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">
                        Toriz
                    </span>
                </div>
                <!-- Close Button (Mobile Only) -->
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="material-icons">dashboard</span>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span class="material-icons">inventory_2</span>
                    <span>Products</span>
                </a>
                
                <a href="{{ route('stock.transactions') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                    <span class="material-icons">swap_horiz</span>
                    <span>Transactions</span>
                </a>

                <div class="pt-6 pb-2">
                    <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Reports</p>
                </div>

                <a href="{{ route('reports.low-stock') }}" class="nav-link {{ request()->routeIs('reports.low-stock') ? 'active' : '' }}">
                    <span class="material-icons">warning</span>
                    <span>Low Stock</span>
                </a>
                
                <a href="{{ route('reports.stock-movement') }}" class="nav-link {{ request()->routeIs('reports.stock-movement') ? 'active' : '' }}">
                    <span class="material-icons">timeline</span>
                    <span>Movement</span>
                </a>
                
                <a href="{{ route('reports.expiry') }}" class="nav-link {{ request()->routeIs('reports.expiry') ? 'active' : '' }}">
                    <span class="material-icons">event_busy</span>
                    <span>Expiry</span>
                </a>
                
                <a href="{{ route('reports.stock-value') }}" class="nav-link {{ request()->routeIs('reports.stock-value') ? 'active' : '' }}">
                    <span class="material-icons">attach_money</span>
                    <span>Stock Value</span>
                </a>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-slate-700/50 bg-slate-800/20">
                <div class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-700/50 transition-colors cursor-pointer group">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 to-orange-500 flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
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
            <header class="h-16 bg-slate-900/30 backdrop-blur-xl border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="md:hidden p-2 -ml-2 mr-2 rounded-lg text-gray-400 hover:text-white hover:bg-slate-700/50">
                        <span class="material-icons">menu</span>
                    </button>
                    <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <input type="search" placeholder="Search..." class="pl-10 pr-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 text-sm w-64 transition-all">
                            <span class="material-icons absolute left-3 top-2.5 text-gray-400 text-lg">search</span>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <button class="relative p-2 rounded-xl text-gray-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                        <span class="material-icons">notifications</span>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-slate-900"></span>
                    </button>

                    <!-- Theme Toggle -->
                    {{-- <button id="theme-toggle" class="p-2 rounded-xl text-gray-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                        <span class="material-icons">light_mode</span>
                    </button> --}}
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 pb-24 md:pb-6 custom-scrollbar">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                         class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl flex items-center backdrop-blur-sm">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                            <span class="material-icons text-green-400 text-sm">check</span>
                        </div>
                        <span class="text-green-100 font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation (Optional - kept for quick access) -->
    <nav class="md:hidden fixed bottom-4 left-4 right-4 z-40 bg-slate-900/90 backdrop-blur-xl border border-slate-700/50 rounded-2xl shadow-xl shadow-black/50">
        <div class="flex justify-around items-center h-16 px-2">
            <a href="{{ route('dashboard') }}" class="mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="material-icons text-2xl mb-0.5">dashboard</span>
            </a>
            <a href="{{ route('products.index') }}" class="mobile-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <span class="material-icons text-2xl mb-0.5">inventory_2</span>
            </a>
            
            <!-- Center Generic FAB for Action -->
            <div class="relative -top-5">
                <a href="{{ route('stock.transactions') }}" class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full shadow-lg shadow-indigo-500/50 text-white transform hover:scale-105 transition-transform">
                    <span class="material-icons">add</span>
                </a>
            </div>

            <a href="{{ route('stock.transactions') }}" class="mobile-nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                <span class="material-icons text-2xl mb-0.5">swap_horiz</span>
            </a>
            <a href="#" @click="sidebarOpen = true" class="mobile-nav-link">
                <span class="material-icons text-2xl mb-0.5">menu</span>
            </a>
        </div>
    </nav>

    @livewireScripts
    
    <style>
        .nav-link {
            @apply flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all duration-200 border border-transparent;
        }
        .nav-link.active {
            @apply bg-indigo-500/10 text-indigo-400 border-indigo-500/20 font-medium shadow-sm;
        }
        .nav-link.active .material-icons {
            @apply text-indigo-400;
        }
        
        /* Mobile Nav Link */
        .mobile-nav-link {
            @apply flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-indigo-400 transition-colors rounded-xl mx-1;
        }
        .mobile-nav-link.active {
            @apply text-indigo-400 bg-white/5;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>

    @stack('scripts')
</body>
</html>