<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['user', 'assignedTechnician']);
        
        // Filtrer selon le rôle
        if ($user->role === 'personnel') {
            $query->where('user_id', $user->id);
        }
        
        // Appliquer les filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }
        
        $tickets = $query->latest()->paginate(20)->withQueryString();
        
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:faible,normale,urgent,critique',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'ouvert',
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket créé avec succès');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        
        $ticket->load(['user', 'assignedTechnician', 'messages.user']);
        
        // Récupérer uniquement les techniciens actifs
        $technicians = User::where('role', 'technicien')
            ->where('is_active', true)
            ->get();
        
        return view('tickets.show', compact('ticket', 'technicians'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        
        $validated = $request->validate([
            'status' => 'sometimes|in:ouvert,en_cours,resolu,ferme',
            'priority' => 'sometimes|in:faible,normale,urgent,critique',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $oldStatus = $ticket->status;
        $oldAssignedTo = $ticket->assigned_to;

        // Vérifier que le technicien assigné est actif
        if (isset($validated['assigned_to'])) {
            $technician = User::find($validated['assigned_to']);
            if ($technician && !$technician->is_active) {
                return back()->with('error', 'Impossible d\'assigner un ticket à un technicien inactif');
            }
        }

        if (isset($validated['status']) && $validated['status'] === 'resolu' && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
        }

        // Mettre à jour le ticket
        $ticket->update($validated);

        // Notification pour assignation de ticket
        if (isset($validated['assigned_to']) && $validated['assigned_to'] != $oldAssignedTo && $validated['assigned_to']) {
            Notification::create([
                'user_id' => $validated['assigned_to'],
                'ticket_id' => $ticket->id,
                'type' => 'ticket_assigned',
                'title' => 'Nouveau ticket assigné',
                'message' => "Le ticket #{$ticket->id} - {$ticket->title} vous a été assigné",
            ]);
        }

        // Notification pour changement de statut (au créateur du ticket)
        if (isset($validated['status']) && $validated['status'] != $oldStatus) {
            Notification::create([
                'user_id' => $ticket->user_id,
                'ticket_id' => $ticket->id,
                'type' => 'status_changed',
                'title' => 'Statut du ticket modifié',
                'message' => "Le statut de votre ticket #{$ticket->id} - {$ticket->title} a été changé en : " . ucfirst(str_replace('_', ' ', $validated['status'])),
            ]);
        }

        return back()->with('success', 'Ticket mis à jour avec succès');
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket supprimé avec succès');
    }

    public function closeTicket(Ticket $ticket)
    {
        // Seul le créateur du ticket peut le fermer
        if ($ticket->user_id !== Auth::id()) {
            return back()->with('error', 'Vous ne pouvez fermer que vos propres tickets');
        }

        $ticket->update(['status' => 'ferme']);

        // Notifier le technicien si assigné
        if ($ticket->assigned_to) {
            Notification::create([
                'user_id' => $ticket->assigned_to,
                'ticket_id' => $ticket->id,
                'type' => 'status_changed',
                'title' => 'Ticket fermé',
                'message' => "Le ticket #{$ticket->id} - {$ticket->title} a été fermé par l'utilisateur",
            ]);
        }

        return back()->with('success', 'Ticket fermé avec succès');
    }

    public function reopenTicket(Ticket $ticket)
    {
        // Seul le créateur du ticket peut le rouvrir
        if ($ticket->user_id !== Auth::id()) {
            return back()->with('error', 'Vous ne pouvez rouvrir que vos propres tickets');
        }

        $ticket->update(['status' => 'ouvert']);

        // Notifier le technicien si assigné
        if ($ticket->assigned_to) {
            Notification::create([
                'user_id' => $ticket->assigned_to,
                'ticket_id' => $ticket->id,
                'type' => 'status_changed',
                'title' => 'Ticket réouvert',
                'message' => "Le ticket #{$ticket->id} - {$ticket->title} a été réouvert par l'utilisateur",
            ]);
        }

        return back()->with('success', 'Ticket réouvert avec succès');
    }
}