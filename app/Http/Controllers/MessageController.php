<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ticket;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        // Créer une notification pour le destinataire du message
        // Si l'auteur du message est le créateur du ticket, notifier le technicien assigné
        // Si l'auteur est le technicien, notifier le créateur du ticket
        $recipientId = null;
        $notificationMessage = '';

        if (Auth::id() === $ticket->user_id && $ticket->assigned_to) {
            // Le créateur envoie un message, notifier le technicien
            $recipientId = $ticket->assigned_to;
            $notificationMessage = "Nouveau message sur le ticket #{$ticket->id} - {$ticket->title}";
        } elseif (Auth::id() === $ticket->assigned_to && $ticket->user_id) {
            // Le technicien envoie un message, notifier le créateur
            $recipientId = $ticket->user_id;
            $notificationMessage = "Le technicien a répondu sur votre ticket #{$ticket->id} - {$ticket->title}";
        } elseif (Auth::user()->isAdmin() && $ticket->user_id !== Auth::id()) {
            // Un admin envoie un message, notifier le créateur
            $recipientId = $ticket->user_id;
            $notificationMessage = "Nouveau message sur votre ticket #{$ticket->id} - {$ticket->title}";
        }

        // Créer la notification si un destinataire est identifié
        if ($recipientId) {
            Notification::create([
                'user_id' => $recipientId,
                'ticket_id' => $ticket->id,
                'type' => 'new_message',
                'title' => 'Nouveau message',
                'message' => $notificationMessage,
            ]);
        }

        return back()->with('success', 'Message envoyé avec succès');
    }
}