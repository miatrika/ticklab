@extends('layouts.app')

@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Vue d\'ensemble complète du système')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Users --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Total Utilisateurs</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Total Tickets --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Total Tickets</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ \App\Models\Ticket::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Open Tickets --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Tickets Ouverts</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ \App\Models\Ticket::where('status', 'ouvert')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder-open text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Resolved Tickets --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Tickets Résolus</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ \App\Models\Ticket::where('status', 'resolu')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-bolt text-blue-400"></i>
                Actions Rapides
            </h3>
            <div class="space-y-3">
                <a href="{{ route('users.create') }}" class="flex items-center gap-3 p-3 bg-purple-500/10 border border-purple-500/30 rounded-lg hover:bg-purple-500/20 transition">
                    <i class="fas fa-user-plus text-purple-400"></i>
                    <span class="font-semibold text-gray-200">Créer un Utilisateur</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 p-3 bg-blue-500/10 border border-blue-500/30 rounded-lg hover:bg-blue-500/20 transition">
                    <i class="fas fa-users-cog text-blue-400"></i>
                    <span class="font-semibold text-gray-200">Gérer les Utilisateurs</span>
                </a>
                <a href="{{ route('tickets.index') }}" class="flex items-center gap-3 p-3 bg-green-500/10 border border-green-500/30 rounded-lg hover:bg-green-500/20 transition">
                    <i class="fas fa-list text-green-400"></i>
                    <span class="font-semibold text-gray-200">Voir Tous les Tickets</span>
                </a>
            </div>
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-blue-400"></i>
                Statistiques par Rôle
            </h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-slate-700 border border-slate-600 rounded-lg">
                    <span class="font-semibold text-gray-300">Administrateurs</span>
                    <span class="text-2xl font-bold text-purple-400">{{ \App\Models\User::where('role', 'admin')->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-700 border border-slate-600 rounded-lg">
                    <span class="font-semibold text-gray-300">Techniciens</span>
                    <span class="text-2xl font-bold text-blue-400">{{ \App\Models\User::where('role', 'technicien')->count() }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-700 border border-slate-600 rounded-lg">
                    <span class="font-semibold text-gray-300">Personnel</span>
                    <span class="text-2xl font-bold text-green-400">{{ \App\Models\User::where('role', 'personnel')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Tickets --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-clock text-blue-400"></i>
                Tickets Récents
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900 border-b border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Priorité</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse(\App\Models\Ticket::with('user')->latest()->take(10)->get() as $ticket)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 text-sm font-semibold text-white">#{{ $ticket->id }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-blue-400 hover:text-blue-300 transition">
                                {{ Str::limit($ticket->title, 40) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $ticket->user->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                @if($ticket->status === 'ouvert') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                @elseif($ticket->status === 'en_cours') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                                @elseif($ticket->status === 'resolu') bg-green-500/20 text-green-400 border border-green-500/30
                                @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                                @endif">
                                <i class="fas fa-circle text-[6px] mr-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                @if($ticket->priority === 'critique') bg-red-500/20 text-red-400 border border-red-500/30
                                @elseif($ticket->priority === 'urgent') bg-orange-500/20 text-orange-400 border border-orange-500/30
                                @else bg-blue-500/20 text-blue-400 border border-blue-500/30
                                @endif">
                                <i class="fas fa-flag text-[8px] mr-2"></i>
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-5xl text-gray-600 mb-4"></i>
                            <p class="text-gray-400 text-lg">Aucun ticket pour le moment</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection