@extends('layouts.app')

@section('page-title', 'Dashboard Technicien')
@section('page-subtitle', 'Vos tickets assignés')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Assigned Tickets --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Tickets Assignés</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ auth()->user()->assignedTickets()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">En Cours</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ auth()->user()->assignedTickets()->where('status', 'en_cours')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Resolved --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Résolus</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ auth()->user()->assignedTickets()->where('status', 'resolu')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Urgent --}}
        <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 mb-1">Urgents</p>
                    <p class="text-3xl font-bold text-white mt-2">{{ auth()->user()->assignedTickets()->whereIn('priority', ['urgent', 'critique'])->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- My Assigned Tickets --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-bold text-white">Mes Tickets Assignés</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900 border-b border-slate-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Priorité</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse(auth()->user()->assignedTickets()->with('user')->latest()->get() as $ticket)
                    <tr class="hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-white">{{ $ticket->title }}</p>
                            <p class="text-sm text-gray-400">{{ Str::limit($ticket->description, 50) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-300">{{ $ticket->user->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full border
                                @if($ticket->status === 'ouvert') bg-blue-500/20 text-blue-400 border-blue-500/30
                                @elseif($ticket->status === 'en_cours') bg-yellow-500/20 text-yellow-400 border-yellow-500/30
                                @elseif($ticket->status === 'resolu') bg-green-500/20 text-green-400 border-green-500/30
                                @else bg-gray-500/20 text-gray-400 border-gray-500/30
                                @endif">
                                <i class="fas fa-circle text-[6px]"></i>
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full border
                                @if($ticket->priority === 'critique') bg-red-500/20 text-red-400 border-red-500/30
                                @elseif($ticket->priority === 'urgent') bg-orange-500/20 text-orange-400 border-orange-500/30
                                @else bg-blue-500/20 text-blue-400 border-blue-500/30
                                @endif">
                                <i class="fas fa-flag text-[8px]"></i>
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-purple-400 hover:text-purple-300 font-semibold text-sm">
                                Traiter →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-5xl text-slate-600 mb-4"></i>
                            <p class="text-slate-400 text-lg">Aucun ticket assigné pour le moment</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
