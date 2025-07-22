<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UtilisateursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Vider la table avant de la peupler
        Utilisateur::truncate();
        
        $utilisateurs = [
            [
                'nom_utilisateur' => 'Ngou Pare',
                'prenom_utilisateur' => 'Ibrahim',
                'email_utilisateur' => 'ibrahim.ngoupare@gmail.com',
                'pass_utilisateur' => Hash::make('123456789'),
                'type_utilisateur' => 'admin',
                'tel_utilisateur' => '0601020304',
                'etat_compte' => 'activé',
                'type_compte' => 'admin',
                'date_inscription' => now(),
                'date_confirmation' => now(),
            ],
            [
                'nom_utilisateur' => 'Pemboura Pare',
                'prenom_utilisateur' => 'Noura',
                'email_utilisateur' => 'noura.pembourapare@gmail.com',
                'pass_utilisateur' => Hash::make('password123'),
                'type_utilisateur' => 'agent',
                'tel_utilisateur' => '0605060708',
                'etat_compte' => 'activé',
                'type_compte' => 'agent',
                'date_inscription' => now(),
                'date_confirmation' => now(),
            ]
        ];

        foreach ($utilisateurs as $utilisateur) {
            Utilisateur::create($utilisateur);
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
