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
        $admin = User::updateOrCreate(
            ['email' => 'admin@ticklab.com'],
            [
                'name' => 'Admin TickLab',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Techniciens
        $tech1 = User::updateOrCreate(
            ['email' => 'tech1@ticklab.com'],
            [
                'name' => 'Jean Technicien',
                'password' => Hash::make('password'),
                'role' => 'technicien',
                'department' => 'Support IT',
            ]
        );

        $tech2 = User::updateOrCreate(
            ['email' => 'tech2@ticklab.com'],
            [
                'name' => 'Marie Technicienne',
                'password' => Hash::make('password'),
                'role' => 'technicien',
                'department' => 'Support IT',
            ]
        );

        // Personnel
        $user1 = User::updateOrCreate(
            ['email' => 'pierre@ticklab.com'],
            [
                'name' => 'Pierre Dupont',
                'password' => Hash::make('password'),
                'role' => 'personnel',
                'department' => 'Comptabilité',
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'sophie@ticklab.com'],
            [
                'name' => 'Sophie Martin',
                'password' => Hash::make('password'),
                'role' => 'personnel',
                'department' => 'Ressources Humaines',
            ]
        );

        // Tickets de test
        Ticket::firstOrCreate([
            'title' => 'Impossible de se connecter au VPN',
            'user_id' => $user1->id,
        ], [
            'description' => 'Je reçois une erreur "Connection timeout" quand j\'essaie de me connecter au VPN de l\'entreprise.',
            'status' => 'ouvert',
            'priority' => 'urgent',
        ]);

        Ticket::firstOrCreate([
            'title' => 'Imprimante du 2ème étage hors service',
            'user_id' => $user2->id,
        ], [
            'description' => 'L\'imprimante Canon du 2ème étage affiche un message d\'erreur E03 et ne répond plus.',
            'status' => 'en_cours',
            'priority' => 'normale',
            'assigned_to' => $tech1->id,
        ]);

        Ticket::firstOrCreate([
            'title' => 'Installation Adobe Creative Suite',
            'user_id' => $user1->id,
        ], [
            'description' => 'J\'ai besoin d\'Adobe Photoshop et Illustrator pour mon nouveau projet.',
            'status' => 'resolu',
            'priority' => 'faible',
            'assigned_to' => $tech2->id,
            'resolved_at' => now()->subDays(2),
        ]);

        Ticket::firstOrCreate([
            'title' => 'Serveur de fichiers inaccessible',
            'user_id' => $user2->id,
        ], [
            'description' => 'Le serveur de fichiers principal (\\\\srv-files01) ne répond pas depuis ce matin.',
            'status' => 'ouvert',
            'priority' => 'critique',
        ]);
    }
}
