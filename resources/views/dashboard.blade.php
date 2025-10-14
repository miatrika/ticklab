@extends('layouts.app')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Vue d\'ensemble de vos tickets')

@section('content')
<div class="space-y-6">
     Stats Cards 
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
         Total Tickets 
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tickets</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->tickets()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

         Open Tickets 
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ouverts</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->tickets()->where('status', 'ouvert')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder-open text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

         In Progress 
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->tickets()->where('status', 'en_cours')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

         Resolved 
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Résolus</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->tickets()->where('status', 'resolu')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

     Recent Tickets 
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900">Tickets Récents</h3>
            <a href="{{ route('tickets.create') }}" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90 transition flex items-center gap-2 text-sm font-semibold">
                <i class="fas fa-plus"></i>
                <span>Nouveau Ticket</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Priorité</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse(auth()->user()->tickets()->latest()->take(5)->get() as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $ticket->title }}</p>
                                <p class="text-sm text-gray-500 truncate max-w-md">{{ Str::limit($ticket->description, 60) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full
                                @if($ticket->status === 'ouvert') bg-yellow-100 text-yellow-800
                                @elseif($ticket->status === 'en_cours') bg-blue-100 text-blue-800
                                @elseif($ticket->status === 'resolu') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                <i class="fas fa-circle text-[8px]"></i>
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full
                                @if($ticket->priority === 'critique') bg-red-100 text-red-800
                                @elseif($ticket->priority === 'urgent') bg-orange-100 text-orange-800
                                @elseif($ticket->priority === 'normale') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($ticket->priority === 'critique' || $ticket->priority === 'urgent')
                                    <i class="fas fa-exclamation-triangle"></i>
                                @endif
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <i class="far fa-clock mr-1"></i>
                            {{ $ticket->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-purple-600 hover:text-purple-800 font-semibold text-sm">
                                Voir détails →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-inbox text-gray-300 text-5xl"></i>
                                <p class="text-gray-500 font-medium">Aucun ticket pour le moment</p>
                                <a href="{{ route('tickets.create') }}" class="gradient-bg text-white px-6 py-2 rounded-lg hover:opacity-90 transition">
                                    Créer votre premier ticket
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(auth()->user()->tickets()->count() > 5)
        <div class="px-6 py-4 bg-gray-50 border-t">
            <a href="{{ route('tickets.index') }}" class="text-purple-600 hover:text-purple-800 font-semibold text-sm flex items-center justify-center gap-2">
                <span>Voir tous les tickets</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @endif
    </div>
</div>
@endsection