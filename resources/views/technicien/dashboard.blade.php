<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Technicien - Helpdesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#0a0a0a',
                        foreground: '#ededed',
                        card: '#141414',
                        'card-foreground': '#ededed',
                        primary: '#3b82f6',
                        'primary-foreground': '#ffffff',
                        secondary: '#1f1f1f',
                        'secondary-foreground': '#ededed',
                        muted: '#1f1f1f',
                        'muted-foreground': '#a1a1a1',
                        accent: '#1f1f1f',
                        'accent-foreground': '#ededed',
                        border: '#2a2a2a',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-foreground min-h-screen">
    <div class="flex">
         Sidebar 
        <aside class="w-64 bg-card border-r border-border min-h-screen p-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-primary">Helpdesk</h1>
                <p class="text-sm text-muted-foreground mt-1">Technicien</p>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('technician.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-primary text-primary-foreground">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-muted-foreground hover:bg-accent hover:text-accent-foreground transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </nav>
        </aside>

         Main Content 
        <main class="flex-1 p-8">
             Header 
            <div class="mb-8">
                <h2 class="text-3xl font-bold mb-2">Bienvenue, {{ Auth::user()->name }}</h2>
                <p class="text-muted-foreground">Gérez vos tickets et prenez en charge de nouveaux tickets</p>
            </div>

             Alerts 
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900/20 border border-green-700 rounded-lg text-green-400">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-900/20 border border-red-700 rounded-lg text-red-400">
                    {{ session('error') }}
                </div>
            @endif

             Stats Cards 
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-card border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Mes Tickets</h3>
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ $stats['my_tickets'] }}</p>
                </div>

                <div class="bg-card border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Disponibles</h3>
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ $stats['available_tickets'] }}</p>
                </div>

                <div class="bg-card border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">En Cours</h3>
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ $stats['in_progress'] }}</p>
                </div>

                <div class="bg-card border border-border rounded-lg p-6">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-muted-foreground">Résolus</h3>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ $stats['resolved_tickets'] }}</p>
                </div>
            </div>

             Tickets Disponibles 
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4">Tickets Disponibles (Non Assignés)</h3>
                <div class="bg-card border border-border rounded-lg overflow-hidden">
                    @if($availableTickets->isEmpty())
                        <div class="p-8 text-center text-muted-foreground">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Aucun ticket disponible pour le moment</p>
                        </div>
                    @else
                        <table class="w-full">
                            <thead class="bg-secondary border-b border-border">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Priorité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($availableTickets as $ticket)
                                    <tr class="hover:bg-accent/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ $ticket->id }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium">{{ $ticket->title }}</div>
                                            <div class="text-sm text-muted-foreground">{{ Str::limit($ticket->description, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $priorityColors = [
                                                    'basse' => 'bg-gray-700 text-gray-300',
                                                    'moyenne' => 'bg-blue-900/50 text-blue-400',
                                                    'haute' => 'bg-orange-900/50 text-orange-400',
                                                    'urgente' => 'bg-red-900/50 text-red-400',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$ticket->priority] }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <form method="POST" action="{{ route('technician.tickets.take', $ticket) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium">
                                                    Prendre en charge
                                                </button>
                                            </form>
                                            <button onclick="openRequestModal({{ $ticket->id }})" class="ml-2 px-4 py-2 bg-secondary text-secondary-foreground border border-border rounded-lg hover:bg-accent transition-colors font-medium">
                                                Demander assignation
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

             Mes Tickets 
            <div>
                <h3 class="text-xl font-bold mb-4">Mes Tickets Assignés</h3>
                <div class="bg-card border border-border rounded-lg overflow-hidden">
                    @if($myTickets->isEmpty())
                        <div class="p-8 text-center text-muted-foreground">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>Vous n'avez aucun ticket assigné</p>
                        </div>
                    @else
                        <table class="w-full">
                            <thead class="bg-secondary border-b border-border">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Titre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Priorité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Client</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach($myTickets as $ticket)
                                    <tr class="hover:bg-accent/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#{{ $ticket->id }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium">{{ $ticket->title }}</div>
                                            <div class="text-sm text-muted-foreground">{{ Str::limit($ticket->description, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'nouveau' => 'bg-blue-900/50 text-blue-400',
                                                    'en_cours' => 'bg-yellow-900/50 text-yellow-400',
                                                    'resolu' => 'bg-green-900/50 text-green-400',
                                                    'ferme' => 'bg-gray-700 text-gray-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$ticket->status] }}">
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $priorityColors = [
                                                    'basse' => 'bg-gray-700 text-gray-300',
                                                    'moyenne' => 'bg-blue-900/50 text-blue-400',
                                                    'haute' => 'bg-orange-900/50 text-orange-400',
                                                    'urgente' => 'bg-red-900/50 text-red-400',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $priorityColors[$ticket->priority] }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $ticket->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('technician.tickets.show', $ticket) }}" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium inline-block">
                                                Voir détails
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </main>
    </div>

     Modal pour demande d'assignation 
    <div id="requestModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-card border border-border rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-xl font-bold mb-4">Demander l'assignation</h3>
            <form id="requestForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Message (optionnel)</label>
                    <textarea name="message" rows="4" class="w-full px-3 py-2 bg-background border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-foreground" placeholder="Expliquez pourquoi vous souhaitez ce ticket..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium">
                        Envoyer la demande
                    </button>
                    <button type="button" onclick="closeRequestModal()" class="flex-1 px-4 py-2 bg-secondary text-secondary-foreground border border-border rounded-lg hover:bg-accent transition-colors font-medium">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRequestModal(ticketId) {
            const modal = document.getElementById('requestModal');
            const form = document.getElementById('requestForm');
            form.action = `/technician/tickets/${ticketId}/request-assignment`;
            modal.classList.remove('hidden');
        }

        function closeRequestModal() {
            const modal = document.getElementById('requestModal');
            modal.classList.add('hidden');
        }

        // Fermer le modal en cliquant en dehors
        document.getElementById('requestModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRequestModal();
            }
        });
    </script>
</body>
</html>
