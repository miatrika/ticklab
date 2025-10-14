@extends('layouts.app')

@section('page-title', 'Modifier l\'Utilisateur')
@section('page-subtitle', 'Mettre à jour les informations de ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8">
        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nom Complet <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        required
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        required
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Téléphone
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                    >
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-semibold text-gray-700 mb-2">
                        Département
                    </label>
                    <input 
                        type="text" 
                        name="department" 
                        id="department"
                        value="{{ old('department', $user->department) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                    >
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                    Rôle <span class="text-red-500">*</span>
                </label>
                <select 
                    name="role" 
                    id="role"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
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

            <div class="border-t pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Changer le Mot de Passe</h3>
                <p class="text-sm text-gray-600 mb-4">Laissez vide pour conserver le mot de passe actuel</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nouveau Mot de Passe
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        >
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirmer le Mot de Passe
                        </label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            id="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                        >
                    </div>
                </div>
            </div>

            <div class="border-t pt-6">
                <label class="flex items-start gap-3 cursor-pointer p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ $user->is_active ? 'checked' : '' }}
                        class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 mt-0.5"
                    >
                    <div>
                        <span class="text-sm font-semibold text-gray-900">Compte actif</span>
                        <p class="text-xs text-gray-600 mt-1">Les utilisateurs inactifs ne peuvent pas se connecter au système</p>
                    </div>
                </label>
            </div>

            <div class="flex gap-4 pt-6">
                <button 
                    type="submit"
                    class="flex-1 gradient-bg text-white font-semibold py-3 rounded-lg hover:opacity-90 transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-save"></i>
                    <span>Enregistrer les Modifications</span>
                </button>
                <a 
                    href="{{ route('users.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection