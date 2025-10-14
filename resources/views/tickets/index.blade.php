@extends('layouts.app')

@section('page-title', 'Tickets')
@section('page-subtitle', 'Gérer tous les tickets')

@section('content')
<div class="space-y-6">
    {{-- Header avec Actions --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Tickets</h1>
            <p class="text-gray-400 mt-1">Gérez et suivez tous vos tickets</p>
        </div>
        @if(auth()->user()->role === 'personnel')
        <a href="{{ route('tickets.create') }}" class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold flex items-center gap-2 shadow-lg shadow-blue-500/30 w-fit">
            <i class="fas fa-plus-circle"></i>
            <span>Nouveau Ticket</span>
        </a>
        @endif
    </div>

    {{-- Filtres --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <form method="GET" action="{{ route('tickets.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Statut</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Tous les statuts</option>
                        <option value="ouvert" {{ request('status') === 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                        <option value="en_cours" {{ request('status') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="resolu" {{ request('status') === 'resolu' ? 'selected' : '' }}>Résolu</option>
                        <option value="ferme" {{ request('status') === 'ferme' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Priorité</label>
                    <select name="priority" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Toutes les priorités</option>
                        <option value="faible" {{ request('priority') === 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="normale" {{ request('priority') === 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="critique" {{ request('priority') === 'critique' ? 'selected' : '' }}>Critique</option>
                    </select>
                </div>

                @if(auth()->user()->isAdmin())
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Assignation</label>
                    <select name="assigned_to" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Tous</option>
                        <option value="unassigned" {{ request('assigned_to') === 'unassigned' ? 'selected' : '' }}>Non assignés</option>
                    </select>
                </div>
                @endif
            </div>

            <div class="flex gap-3">
                <button type="submit" class="gradient-bg text-white px-6 py-2.5 rounded-lg hover:opacity-90 transition font-semibold shadow-lg shadow-blue-500/30 flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Filtrer</span>
                </button>
                <a href="{{ route('tickets.index') }}" class="px-6 py-2.5 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition font-semibold flex items-center gap-2">
                    <i class="fas fa-redo"></i>
                    <span>Réinitialiser</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Liste des Tickets --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        @forelse($tickets as $ticket)
        <div class="p-6 border-b border-slate-700 hover:bg-slate-700/50 transition" x-data="{ formId: 'delete-ticket-{{ $ticket->id }}' }">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('tickets.show', $ticket) }}" class="text-lg font-bold text-white hover:text-blue-400 transition">
                            #{{ $ticket->id }} - {{ $ticket->title }}
                        </a>
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                            @if($ticket->status === 'ouvert') bg-blue-500/20 text-blue-400 border border-blue-500/30
                            @elseif($ticket->status === 'en_cours') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                            @elseif($ticket->status === 'resolu') bg-green-500/20 text-green-400 border border-green-500/30
                            @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full
                            @if($ticket->priority === 'critique') bg-red-500/20 text-red-400 border border-red-500/30
                            @elseif($ticket->priority === 'urgent') bg-orange-500/20 text-orange-400 border border-orange-500/30
                            @elseif($ticket->priority === 'normale') bg-blue-500/20 text-blue-400 border border-blue-500/30
                            @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                            @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                    <p class="text-gray-300 text-sm mb-3 line-clamp-2">{{ $ticket->description }}</p>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-user text-xs"></i>
                            {{ $ticket->user->name }}
                        </span>
                        @if($ticket->assignedTechnician)
                        <span class="flex items-center gap-1">
                            <i class="fas fa-user-tag text-xs"></i>
                            {{ $ticket->assignedTechnician->name }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar text-xs"></i>
                            {{ $ticket->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('tickets.show', $ticket) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm flex items-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>Voir</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                    <button 
                        type="button"
                        @click="$dispatch('open-modal-delete-ticket-{{ $ticket->id }}')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm flex items-center gap-2"
                    >
                        <i class="fas fa-trash"></i>
                    </button>

                    <form 
                        id="delete-ticket-{{ $ticket->id }}"
                        method="POST" 
                        action="{{ route('tickets.destroy', $ticket) }}"
                        x-on:confirm-action-delete-ticket-{{ $ticket->id }}.window="document.getElementById(formId).submit()"
                    >
                        @csrf
                        @method('DELETE')
                    </form>

                    <x-confirm-modal 
                        id="delete-ticket-{{ $ticket->id }}"
                        title="Supprimer le ticket #{{ $ticket->id }} ?"
                        message="Cette action est irréversible. Toutes les données associées à ce ticket seront définitivement supprimées."
                        confirm-text="Oui, supprimer"
                        cancel-text="Annuler"
                        type="danger"
                    />
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-5xl text-gray-600 mb-4"></i>
            <p class="text-gray-400 text-lg">Aucun ticket trouvé</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="flex justify-center">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection