@extends('layouts.app')

@section('page-title', 'Ticket #' . $ticket->id)
@section('page-subtitle', $ticket->title)

@section('content')
<div class="space-y-6">
    {{-- Retour et Actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('tickets.index') }}" class="text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Retour aux tickets</span>
        </a>

        {{-- Bouton Supprimer (Admin uniquement) --}}
        @if(auth()->user()->isAdmin())
        <div x-data="{ formId: 'delete-ticket-form-{{ $ticket->id }}' }">
            <button 
                type="button"
                @click="$dispatch('open-modal-delete-ticket')"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center gap-2 shadow-lg shadow-red-500/30"
            >
                <i class="fas fa-trash"></i>
                <span>Supprimer le Ticket</span>
            </button>

            <form 
                id="delete-ticket-form-{{ $ticket->id }}"
                method="POST" 
                action="{{ route('tickets.destroy', $ticket) }}"
                x-on:confirm-action-delete-ticket.window="document.getElementById(formId).submit()"
            >
                @csrf
                @method('DELETE')
            </form>
        </div>

        <x-confirm-modal 
            id="delete-ticket"
            title="Supprimer ce ticket ?"
            message="Cette action est irréversible. Toutes les données associées à ce ticket, y compris les messages, seront définitivement supprimées."
            confirm-text="Oui, supprimer"
            cancel-text="Annuler"
            type="danger"
        />
        @endif
    </div>

    {{-- Informations du Ticket --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-white mb-3">{{ $ticket->title }}</h1>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                        @if($ticket->status === 'ouvert') bg-blue-500/20 text-blue-400 border border-blue-500/30
                        @elseif($ticket->status === 'en_cours') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                        @elseif($ticket->status === 'resolu') bg-green-500/20 text-green-400 border border-green-500/30
                        @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                        @endif">
                        <i class="fas fa-circle text-[6px] mr-2"></i>
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                        @if($ticket->priority === 'critique') bg-red-500/20 text-red-400 border border-red-500/30
                        @elseif($ticket->priority === 'urgent') bg-orange-500/20 text-orange-400 border border-orange-500/30
                        @elseif($ticket->priority === 'normale') bg-blue-500/20 text-blue-400 border border-blue-500/30
                        @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                        @endif">
                        <i class="fas fa-flag text-[8px] mr-2"></i>
                        Priorité: {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-400 mb-2">Créé par</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-500/30">
                        {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ $ticket->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $ticket->user->email }}</p>
                    </div>
                </div>
            </div>

            <div>
                <p class="text-sm text-gray-400 mb-2">Assigné à</p>
                @if($ticket->assignedTechnician)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-blue-500/30">
                        {{ strtoupper(substr($ticket->assignedTechnician->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ $ticket->assignedTechnician->name }}</p>
                        <p class="text-xs text-gray-400">{{ $ticket->assignedTechnician->email }}</p>
                    </div>
                </div>
                @else
                <p class="text-gray-500 italic flex items-center gap-2">
                    <i class="fas fa-user-slash text-sm"></i>
                    Non assigné
                </p>
                @endif
            </div>
        </div>

        <div class="border-t border-slate-700 pt-4">
            <p class="text-sm text-gray-400 mb-2 font-semibold">Description</p>
            <p class="text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $ticket->description }}</p>
        </div>

        <div class="border-t border-slate-700 pt-4 mt-4 flex items-center justify-between text-sm text-gray-400">
            <span class="flex items-center gap-2">
                <i class="fas fa-calendar-alt"></i>
                Créé le {{ $ticket->created_at->format('d/m/Y à H:i') }}
            </span>
            @if($ticket->resolved_at)
            <span class="text-green-400 font-semibold flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                Résolu le {{ $ticket->resolved_at->format('d/m/Y à H:i') }}
            </span>
            @endif
        </div>
    </div>

    {{-- Gestion du Ticket (Admin/Technicien) --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isTechnicien())
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-cog text-blue-400"></i>
            Gérer le Ticket
        </h3>
        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Statut</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="ouvert" {{ $ticket->status === 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                        <option value="en_cours" {{ $ticket->status === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="resolu" {{ $ticket->status === 'resolu' ? 'selected' : '' }}>Résolu</option>
                        <option value="ferme" {{ $ticket->status === 'ferme' ? 'selected' : '' }}>Fermé</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Priorité</label>
                    <select name="priority" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="faible" {{ $ticket->priority === 'faible' ? 'selected' : '' }}>Faible</option>
                        <option value="normale" {{ $ticket->priority === 'normale' ? 'selected' : '' }}>Normale</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        <option value="critique" {{ $ticket->priority === 'critique' ? 'selected' : '' }}>Critique</option>
                    </select>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div>
                <label class="block text-sm font-semibold text-gray-300 mb-2">Assigner à</label>
                <select name="assigned_to" class="w-full px-4 py-2.5 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Non assigné</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $ticket->assigned_to == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <button type="submit" class="gradient-bg text-white px-6 py-2.5 rounded-lg hover:opacity-90 transition font-semibold shadow-lg shadow-blue-500/30 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Mettre à jour</span>
            </button>
        </form>
    </div>
    @endif

    {{-- Messages --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-comments text-blue-400"></i>
            Messages
        </h3>
        
        <div class="space-y-4 mb-6">
            @forelse($ticket->messages as $message)
            <div class="flex gap-3 {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full {{ $message->user_id === auth()->id() ? 'gradient-bg shadow-lg shadow-blue-500/30' : 'bg-slate-600' }} flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($message->user->name, 0, 1)) }}
                    </div>
                </div>
                <div class="flex-1 max-w-xl">
                    <div class="rounded-lg p-4 {{ $message->user_id === auth()->id() ? 'bg-blue-600/20 border border-blue-500/30' : 'bg-slate-700 border border-slate-600' }}">
                        <div class="flex items-center gap-2 mb-2">
                            <p class="font-semibold text-sm {{ $message->user_id === auth()->id() ? 'text-blue-300' : 'text-white' }}">
                                {{ $message->user->name }}
                            </p>
                            <span class="text-xs text-gray-400">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-200 whitespace-pre-wrap leading-relaxed">{{ $message->content }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <i class="fas fa-comment-slash text-4xl text-gray-600 mb-3"></i>
                <p class="text-gray-500">Aucun message pour le moment</p>
            </div>
            @endforelse
        </div>

        {{-- Formulaire de Message --}}
        <form method="POST" action="{{ route('messages.store', $ticket) }}" class="border-t border-slate-700 pt-4">
            @csrf
            <div class="space-y-3">
                <textarea 
                    name="content" 
                    rows="3" 
                    placeholder="Écrivez votre message..."
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    required
                ></textarea>
                <button type="submit" class="gradient-bg text-white px-6 py-2.5 rounded-lg hover:opacity-90 transition font-semibold shadow-lg shadow-blue-500/30 flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Envoyer</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Actions du Créateur du Ticket (en bas) --}}
    @if(auth()->id() === $ticket->user_id)
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-tools text-blue-400"></i>
            Actions sur le Ticket
        </h3>
        <div class="flex flex-col md:flex-row gap-3">
            @if($ticket->status !== 'ferme')
            <div x-data="{ formId: 'close-ticket-form-{{ $ticket->id }}' }">
                <button 
                    type="button"
                    @click="$dispatch('open-modal-close-ticket')"
                    class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center gap-2 shadow-lg shadow-red-500/30"
                >
                    <i class="fas fa-times-circle"></i>
                    <span>Fermer le Ticket</span>
                </button>

                <form 
                    id="close-ticket-form-{{ $ticket->id }}"
                    method="POST" 
                    action="{{ route('tickets.close', $ticket) }}"
                    x-on:confirm-action-close-ticket.window="document.getElementById(formId).submit()"
                >
                    @csrf
                </form>
            </div>

            <x-confirm-modal 
                id="close-ticket"
                title="Fermer ce ticket ?"
                message="Vous confirmez que le problème est résolu et souhaitez fermer ce ticket. Vous pourrez le rouvrir si nécessaire."
                confirm-text="Oui, fermer"
                cancel-text="Annuler"
                type="danger"
            />
            @endif

            @if($ticket->status === 'ferme')
            <div x-data="{ formId: 'reopen-ticket-form-{{ $ticket->id }}' }">
                <button 
                    type="button"
                    @click="$dispatch('open-modal-reopen-ticket')"
                    class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center gap-2 shadow-lg shadow-green-500/30"
                >
                    <i class="fas fa-redo"></i>
                    <span>Rouvrir le Ticket</span>
                </button>

                <form 
                    id="reopen-ticket-form-{{ $ticket->id }}"
                    method="POST" 
                    action="{{ route('tickets.reopen', $ticket) }}"
                    x-on:confirm-action-reopen-ticket.window="document.getElementById(formId).submit()"
                >
                    @csrf
                </form>
            </div>

            <x-confirm-modal 
                id="reopen-ticket"
                title="Rouvrir ce ticket ?"
                message="Le problème n'est pas résolu ? Vous pouvez rouvrir ce ticket pour continuer à travailler dessus."
                confirm-text="Oui, rouvrir"
                cancel-text="Annuler"
                type="info"
            />
            @endif

            <div class="flex items-center text-sm text-gray-300 bg-blue-500/10 border border-blue-500/30 px-4 py-2.5 rounded-lg">
                <i class="fas fa-info-circle mr-2 text-blue-400"></i>
                @if($ticket->status === 'ferme')
                    <span>Vous pouvez rouvrir ce ticket si le problème n'est pas résolu</span>
                @else
                    <span>Fermez ce ticket si le problème est résolu à votre satisfaction</span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection