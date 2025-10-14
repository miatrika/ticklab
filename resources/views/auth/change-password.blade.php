<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le Mot de Passe - TickLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
                <i class="fas fa-key text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Changement Obligatoire</h1>
            <p class="text-white/80">Vous devez changer votre mot de passe pour continuer</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Première connexion détectée</p>
                        <p class="text-sm mt-1">Pour des raisons de sécurité, veuillez définir un nouveau mot de passe.</p>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="font-semibold">Erreurs :</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Mot de Passe Actuel
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            name="current_password" 
                            id="current_password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nouveau Mot de Passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            name="new_password" 
                            id="new_password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirmer le Nouveau Mot de Passe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-check-circle text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            name="new_password_confirmation" 
                            id="new_password_confirmation"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>

                <button 
                    type="submit"
                    class="w-full gradient-bg text-white font-semibold py-3 rounded-lg hover:opacity-90 transition flex items-center justify-center gap-2"
                >
                    <i class="fas fa-save"></i>
                    <span>Changer le Mot de Passe</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-800">
                        <i class="fas fa-sign-out-alt"></i> Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
