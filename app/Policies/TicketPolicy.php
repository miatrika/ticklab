<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() 
            || $user->isTechnicien() 
            || $ticket->user_id === $user->id 
            || $ticket->assigned_to === $user->id;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() 
            || $user->isTechnicien() 
            || $ticket->user_id === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user): bool
    {
        return $user->isAdmin() || $user->isTechnicien();
    }
}