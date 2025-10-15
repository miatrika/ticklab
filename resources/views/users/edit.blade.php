@extends('layouts.app')

@section('page-title', 'Modifier l\'Utilisateur')
@section('page-subtitle', 'Mettre à jour les informations de ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Informations de base --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">
                        Nom Complet <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition placeholder-gray-500"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition placeholder-gray-500"
                        required
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Téléphone & Département --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-300 mb-2">
                        Téléphone
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition placeholder-gray-500"
                    >
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-semibold text-gray-300 mb-2">
                        Département
                    </label>
                    <input 
                        type="text" 
                        name="department" 
                        id="department"
                        value="{{ old('department', $user->department) }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition placeholder-gray-500"
                    >
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Rôle --}}
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-300 mb-2">
                    Rôle <span class="text-red-500">*</span>
                </label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    required
                >
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="technicien" {{ old('role', $user->role) === 'technicien' ? 'selected' : '' }}>Technicien</option>
                    <option value="personnel" {{ old('role', $user->role) === 'personnel' ? 'selected' : '' }}>Personnel</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Changement du mot de passe --}}
            <div class="border-t border-slate-700 pt-6">
                <h3 class="text-lg font-bold text-gray-100 mb-4">Changer le Mot de Passe</h3>
                <p class="text-sm text-gray-400 mb-4">Laissez vide pour conserver le mot de passe actuel</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">
                            Nouveau Mot de Passe
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-300 mb-2">
                            Confirmer le Mot de Passe
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                        >
                    </div>
                </div>
            </div>

            {{-- Activation du compte --}}
            <div class="border-t border-slate-700 pt-6">
                <label class="flex items-start gap-3 cursor-pointer p-4 bg-slate-900 border border-slate-700 rounded-lg hover:bg-slate-700/50 transition">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ $user->is_active ? 'checked' : '' }}
                        class="w-5 h-5 text-indigo-500 border-slate-600 rounded focus:ring-indigo-500 mt-0.5"
                    >
                    <div>
                        <span class="text-sm font-semibold text-gray-200">Compte actif</span>
                        <p class="text-xs text-gray-400 mt-1">Les utilisateurs inactifs ne peuvent pas se connecter au système</p>
                    </div>
                </label>
            </div>

            {{-- Boutons d’action --}}
            <div class="flex gap-4 pt-6">
                <button 
                    type="submit"
                    class="flex-1 bg-indigo-600 text-white font-semibold py-3 rounded-lg hover:bg-indigo-500 transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les Modifications</span>
                </button>
                <a 
                    href="{{ route('users.index') }}"
                    class="px-6 py-3 bg-slate-700 text-gray-200 font-semibold rounded-lg hover:bg-slate-600 transition"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
