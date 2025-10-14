<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TickLab - Gestion de Tickets')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        [x-cloak] { display: none !important; }
        
        body {
            background-color: #0f172a;
        }
        
        .glass-effect {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.1);
        }
    </style>
</head>
<body class="bg-slate-900 text-gray-100">
    <div class="flex h-screen overflow-hidden">
        @auth
        {{-- Sidebar --}}
        <aside class="w-64 bg-slate-950 border-r border-slate-800 flex-shrink-0 hidden md:block">
            <div class="h-full flex flex-col">
                {{-- Logo --}}
                <div class="p-6 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/50">
                            <i class="fas fa-ticket-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold gradient-text">TickLab</h1>
                            <p class="text-xs text-gray-500">Gestion de Tickets</p>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="font-semibold">Dashboard</span>
                        </a>
                        <a href="{{ route('tickets.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('tickets.*') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-ticket-alt w-5"></i>
                            <span class="font-semibold">Tickets</span>
                        </a>
                        <a href="{{ route('users.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('users.*') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-users w-5"></i>
                            <span class="font-semibold">Utilisateurs</span>
                        </a>
                    @elseif(auth()->user()->isTechnicien())
                        <a href="{{ route('technicien.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('technicien.dashboard') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="font-semibold">Dashboard</span>
                        </a>
                        <a href="{{ route('tickets.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('tickets.*') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-ticket-alt w-5"></i>
                            <span class="font-semibold">Tickets</span>
                        </a>
                    @else
                        <a href="{{ route('tickets.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('tickets.*') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-ticket-alt w-5"></i>
                            <span class="font-semibold">Mes Tickets</span>
                        </a>
                        <a href="{{ route('tickets.create') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('tickets.create') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                            <i class="fas fa-plus-circle w-5"></i>
                            <span class="font-semibold">Nouveau Ticket</span>
                        </a>
                    @endif

                    <a href="{{ route('notifications.index') }}" 
                       class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition {{ request()->routeIs('notifications.*') ? 'bg-slate-800 text-blue-400' : 'text-gray-400' }}">
                        <i class="fas fa-bell w-5"></i>
                        <span class="font-semibold">Notifications</span>
                        @if(auth()->user()->unreadNotificationsCount() > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            {{ auth()->user()->unreadNotificationsCount() }}
                        </span>
                        @endif
                    </a>
                </nav>

                {{-- User Info --}}
                <div class="p-4 border-t border-slate-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 gradient-bg rounded-full flex items-center justify-center font-bold shadow-lg shadow-blue-500/30">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold truncate text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 rounded-lg transition font-semibold text-gray-300">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        @endauth

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            @auth
            {{-- Header --}}
            <header class="bg-slate-900 border-b border-slate-800 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-sm text-gray-400">@yield('page-subtitle', 'Bienvenue sur TickLab')</p>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Notifications Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-white transition">
                                <i class="fas fa-bell text-xl"></i>
                                @if(auth()->user()->unreadNotificationsCount() > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ auth()->user()->unreadNotificationsCount() }}
                                </span>
                                @endif
                            </button>

                            <div 
                                x-show="open" 
                                @click.away="open = false"
                                x-transition
                                class="absolute right-0 mt-2 w-80 bg-slate-800 rounded-xl shadow-2xl border border-slate-700 z-50"
                                style="display: none;"
                            >
                                <div class="p-4 border-b border-slate-700 flex items-center justify-between">
                                    <h3 class="font-bold text-white">Notifications</h3>
                                    @if(auth()->user()->unreadNotificationsCount() > 0)
                                    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-400 hover:text-blue-300 font-semibold">
                                            Tout marquer comme lu
                                        </button>
                                    </form>
                                    @endif
                                </div>

                                <div class="max-h-96 overflow-y-auto">
                                    @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notification)
                                    <a href="{{ route('notifications.read', $notification->id) }}" 
                                       class="block p-4 hover:bg-slate-700 transition border-b border-slate-700 {{ !$notification->is_read ? 'bg-slate-700/50' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full 
                                                    {{ $notification->type === 'ticket_assigned' ? 'bg-blue-500/20' : ($notification->type === 'new_message' ? 'bg-purple-500/20' : 'bg-green-500/20') }} 
                                                    flex items-center justify-center">
                                                    <i class="fas 
                                                        {{ $notification->type === 'ticket_assigned' ? 'fa-user-tag' : ($notification->type === 'new_message' ? 'fa-comment' : 'fa-sync-alt') }} 
                                                        {{ $notification->type === 'ticket_assigned' ? 'text-blue-400' : ($notification->type === 'new_message' ? 'text-purple-400' : 'text-green-400') }}">
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-white">{{ $notification->title }}</p>
                                                <p class="text-xs text-gray-400 mt-1">{{ $notification->message }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if(!$notification->is_read)
                                            <div class="flex-shrink-0">
                                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                                            </div>
                                            @endif
                                        </div>
                                    </a>
                                    @empty
                                    <div class="p-8 text-center text-gray-500">
                                        <i class="fas fa-bell-slash text-3xl mb-2"></i>
                                        <p class="text-sm">Aucune notification</p>
                                    </div>
                                    @endforelse
                                </div>

                                @if(auth()->user()->notifications()->count() > 5)
                                <div class="p-3 border-t border-slate-700 text-center">
                                    <a href="{{ route('notifications.index') }}" class="text-sm text-blue-400 hover:text-blue-300 font-semibold">
                                        Voir toutes les notifications
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            @endauth

            {{-- Content Area --}}
            <main class="flex-1 overflow-y-auto p-6">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-500/10 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-500/10 border border-red-500/50 text-red-400 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="font-semibold">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-yellow-500/10 border border-yellow-500/50 text-yellow-400 px-4 py-3 rounded-lg flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="font-semibold">{{ session('warning') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>