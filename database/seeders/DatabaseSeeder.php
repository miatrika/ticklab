<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin TickLab',
            'email' => 'admin@ticklab.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Techniciens
        $tech1 = User::create([
            'name' => 'Jean Technicien',
            'email' => 'tech1@ticklab.com',
            'password' => Hash::make('password'),
            'role' => 'technicien',
            'department' => 'Support IT',
        ]);

        $tech2 = User::create([
            'name' => 'Marie Technicienne',
            'email' => 'tech2@ticklab.com',
            'password' => Hash::make('password'),
            'role' => 'technicien',
            'department' => 'Support IT',
        ]);

        // Personnel
        $user1 = User::create([
            'name' => 'Pierre Dupont',
            'email' => 'pierre@ticklab.com',
            'password' => Hash::make('password'),
            'role' => 'personnel',
            'department' => 'Comptabilité',
        ]);

        $user2 = User::create([
            'name' => 'Sophie Martin',
            'email' => 'sophie@ticklab.com',
            'password' => Hash::make('password'),
            'role' => 'personnel',
            'department' => 'Ressources Humaines',
        ]);

        // Tickets de test
        Ticket::create([
            'title' => 'Impossible de se connecter au VPN',
            'description' => 'Je reçois une erreur "Connection timeout" quand j\'essaie de me connecter au VPN de l\'entreprise.',
            'status' => 'ouvert',
            'priority' => 'urgent',
            'user_id' => $user1->id,
        ]);

        Ticket::create([
            'title' => 'Imprimante du 2ème étage hors service',
            'description' => 'L\'imprimante Canon du 2ème étage affiche un message d\'erreur E03 et ne répond plus.',
            'status' => 'en_cours',
            'priority' => 'normale',
            'user_id' => $user2->id,
            'assigned_to' => $tech1->id,
        ]);

        Ticket::create([
            'title' => 'Installation Adobe Creative Suite',
            'description' => 'J\'ai besoin d\'Adobe Photoshop et Illustrator pour mon nouveau projet.',
            'status' => 'resolu',
            'priority' => 'faible',
            'user_id' => $user1->id,
            'assigned_to' => $tech2->id,
            'resolved_at' => now()->subDays(2),
        ]);

        Ticket::create([
            'title' => 'Serveur de fichiers inaccessible',
            'description' => 'Le serveur de fichiers principal (\\\\srv-files01) ne répond pas depuis ce matin.',
            'status' => 'ouvert',
            'priority' => 'critique',
            'user_id' => $user2->id,
        ]);
    }
}