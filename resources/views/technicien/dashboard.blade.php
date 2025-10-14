@extends('layouts.app')

@section('page-title', 'Dashboard Technicien')
@section('page-subtitle', 'Vos tickets assignés')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Assigned Tickets --}}
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tickets Assignés</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->assignedTickets()->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">En Cours</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->assignedTickets()->where('status', 'en_cours')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Resolved --}}
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Résolus</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->assignedTickets()->where('status', 'resolu')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        {{-- Urgent --}}
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Urgents</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ auth()->user()->assignedTickets()->whereIn('priority', ['urgent', 'critique'])->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- My Assigned Tickets --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Mes Tickets Assignés</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Priorité</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse(auth()->user()->assignedTickets()->with('user')->latest()->get() as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $ticket->title }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($ticket->description, 50) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->user->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full
                                @if($ticket->status === 'ouvert') bg-yellow-100 text-yellow-800
                                @elseif($ticket->status === 'en_cours') bg-blue-100 text-blue-800
                                @elseif($ticket->status === 'resolu') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full
                                @if($ticket->priority === 'critique') bg-red-100 text-red-800
                                @elseif($ticket->priority === 'urgent') bg-orange-100 text-orange-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('tickets.show', $ticket) }}" class="text-purple-600 hover:text-purple-800 font-semibold text-sm">
                                Traiter →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            Aucun ticket assigné pour le moment
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
