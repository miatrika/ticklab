@extends('layouts.app')

@section('page-title', 'Nouveau Ticket')
@section('page-subtitle', 'Créer une nouvelle demande d\'assistance')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-slate-800 border border-slate-700 rounded-xl shadow-xl p-6">
        <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
            @csrf

            {{-- Titre --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-heading mr-2 text-blue-400"></i>
                    Titre du ticket
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    placeholder="Résumé court du problème"
                    required
                >
                @error('title')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-align-left mr-2 text-blue-400"></i>
                    Description détaillée
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="6"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition placeholder-gray-500"
                    placeholder="Décrivez votre problème en détail..."
                    required
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Plus vous donnez de détails, plus vite nous pourrons vous aider
                </p>
            </div>

            {{-- Priorité --}}
            <div>
                <label for="priority" class="block text-sm font-semibold text-gray-300 mb-2">
                    <i class="fas fa-flag mr-2 text-blue-400"></i>
                    Priorité
                </label>
                <select 
                    name="priority" 
                    id="priority"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    required
                >
                    <option value="faible" {{ old('priority') === 'faible' ? 'selected' : '' }}>Faible - Peut attendre</option>
                    <option value="normale" {{ old('priority', 'normale') === 'normale' ? 'selected' : '' }}>Normale - Traitement standard</option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent - Nécessite une attention rapide</option>
                    <option value="critique" {{ old('priority') === 'critique' ? 'selected' : '' }}>Critique - Bloque mon travail</option>
                </select>
                @error('priority')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                <div class="flex gap-3">
                    <i class="fas fa-lightbulb text-blue-400 text-xl flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="font-semibold text-blue-300 mb-1">Conseils pour un ticket efficace</h4>
                        <ul class="text-sm text-gray-300 space-y-1">
                            <li>• Soyez précis dans votre titre</li>
                            <li>• Décrivez les étapes pour reproduire le problème</li>
                            <li>• Indiquez ce que vous avez déjà essayé</li>
                            <li>• Ajoutez des captures d'écran si nécessaire</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Boutons --}}
            <div class="flex items-center gap-3 pt-4 border-t border-slate-700">
                <button 
                    type="submit" 
                    class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition font-semibold shadow-lg shadow-blue-500/30 flex items-center gap-2"
                >
                    <i class="fas fa-paper-plane"></i>
                    <span>Créer le ticket</span>
                </button>
                <a 
                    href="{{ route('tickets.index') }}" 
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