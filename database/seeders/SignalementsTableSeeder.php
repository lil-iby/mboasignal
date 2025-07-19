<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Signalement;
use Illuminate\Support\Facades\DB;

class SignalementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Vider la table avant de la peupler
        DB::table('signalements')->truncate();
        
        $signalements = [
            [
                'nom_signalement' => 'Nid de poule avenue des Champs-Élysées',
                'description_signalement' => 'Gros nid de poule dangereux pour la circulation sur la voie de droite en direction de l\'Étoile',
                'date_enregistrement' => now()->subDays(5),
                'latitude' => 48.8698,
                'longitude' => 2.3079,
                'etat_signalement' => 'nouveau',
                'statut_signalement' => 'non traité',
                'id_categorie' => 1, // Infrastructure
                'id_organisme' => 1, // Mairie de Paris
                'utilisateur_id' => 3, // Pierre Dubois (citoyen)
            ],
            [
                'nom_signalement' => 'Poubelle pleine au parc Monceau',
                'description_signalement' => 'Les poubelles débordent et les déchets s\'éparpillent dans le parc',
                'date_enregistrement' => now()->subDays(3),
                'latitude' => 48.8796,
                'longitude' => 2.3079,
                'etat_signalement' => 'en cours',
                'statut_signalement' => 'en cours de traitement',
                'id_categorie' => 2, // Propreté
                'id_organisme' => 1, // Mairie de Paris
                'utilisateur_id' => 3, // Pierre Dubois (citoyen)
            ],
            [
                'nom_signalement' => 'Feu de signalisation défectueux',
                'description_signalement' => 'Le feu piéton au croisement de la rue de Rivoli et de la rue du Louvre reste constamment rouge',
                'date_enregistrement' => now()->subDay(),
                'latitude' => 48.8606,
                'longitude' => 2.3376,
                'etat_signalement' => 'en cours',
                'statut_signalement' => 'en cours de traitement',
                'id_categorie' => 3, // Voirie
                'id_organisme' => 1, // Mairie de Paris
                'utilisateur_id' => 2, // Sophie Martin (agent)
            ]
        ];

        foreach ($signalements as $signalement) {
            Signalement::create($signalement);
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
