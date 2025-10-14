@extends('layouts.app')

@section('page-title', 'Utilisateurs')
@section('page-subtitle', 'Gérer les utilisateurs du système')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Utilisateurs</h1>
            <p class="text-gray-400 mt-1">Gérez les comptes utilisateurs</p>
        </div>
        <a href="{{ route('users.create') }}" class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold flex items-center gap-2 shadow-lg shadow-blue-500/30 w-fit">
            <i class="fas fa-user-plus"></i>
            <span>Nouvel Utilisateur</span>
        </a>
    </div>

    {{-- Liste des Utilisateurs --}}
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-900 border-b border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Créé le</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-700/50 transition" x-data="{ formId: 'delete-user-{{ $user->id }}' }">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full gradient-bg flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/30">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-300">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                @if($user->role === 'admin') bg-purple-500/20 text-purple-400 border border-purple-500/30
                                @elseif($user->role === 'technicien') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                @else bg-gray-500/20 text-gray-400 border border-gray-500/30
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full
                                {{ $user->is_active ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                <i class="fas fa-circle text-[6px] mr-2"></i>
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-sm">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold text-sm flex items-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Modifier</span>
                                </a>

                                @if($user->id !== auth()->id())
                                <button 
                                    type="button"
                                    @click="$dispatch('open-modal-delete-user-{{ $user->id }}')"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold text-sm flex items-center gap-2"
                                >
                                    <i class="fas fa-trash"></i>
                                    <span>Supprimer</span>
                                </button>

                                <form 
                                    id="delete-user-{{ $user->id }}"
                                    method="POST" 
                                    action="{{ route('users.destroy', $user) }}"
                                    x-on:confirm-action-delete-user-{{ $user->id }}.window="document.getElementById(formId).submit()"
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <x-confirm-modal 
                                    id="delete-user-{{ $user->id }}"
                                    title="Supprimer {{ $user->name }} ?"
                                    message="Cette action est irréversible. L'utilisateur et toutes ses données associées seront définitivement supprimés."
                                    confirm-text="Oui, supprimer"
                                    cancel-text="Annuler"
                                    type="danger"
                                />
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-users-slash text-5xl text-gray-600 mb-4"></i>
                            <p class="text-gray-400 text-lg">Aucun utilisateur trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="flex justify-center">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection