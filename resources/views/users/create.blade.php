@extends('layouts.app')

@section('page-title', 'Nouvel Utilisateur')
@section('page-subtitle', 'Créer un nouveau compte utilisateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
            @csrf

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-user mr-2 text-blue-400"></i>
                    Nom complet
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    placeholder="Jean Dupont"
                    required
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-envelope mr-2 text-blue-400"></i>
                    Adresse email
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    placeholder="jean.dupont@example.com"
                    required
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-400"></i>
                    Mot de passe
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    placeholder="••••••••"
                    required
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmation mot de passe --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-400"></i>
                    Confirmer le mot de passe
                </label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    placeholder="••••••••"
                    required
                >
            </div>

            {{-- Rôle --}}
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-user-tag mr-2 text-blue-400"></i>
                    Rôle
                </label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required
                >
                    <option value="personnel" {{ old('role') === 'personnel' ? 'selected' : '' }}>Personnel</option>
                    <option value="technicien" {{ old('role') === 'technicien' ? 'selected' : '' }}>Technicien</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrateur</option>
                </select>
                @error('role')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Statut actif --}}
            <div class="flex items-center gap-3 p-4 bg-slate-700 border border-slate-600 rounded-lg">
                <input 
                    type="checkbox" 
                    name="is_active" 
                    id="is_active" 
                    value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-5 h-5 text-blue-600 bg-slate-900 border-slate-600 rounded focus:ring-2 focus:ring-blue-500"
                >
                <label for="is_active" class="text-sm font-semibold text-gray-300">
                    Compte actif
                </label>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center gap-3 pt-4 border-t border-slate-700">
                <button 
                    type="submit" 
                    class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-lg shadow-blue-500/30 flex items-center gap-2"
                >
                    <i class="fas fa-save"></i>
                    <span>Créer l'utilisateur</span>
                </button>
                <a 
                    href="{{ route('users.index') }}" 
                    class="px-6 py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition font-semibold flex items-center gap-2"
                >
                    <i class="fas fa-times"></i>
                    <span>Annuler</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection