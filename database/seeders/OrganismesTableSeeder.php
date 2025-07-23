<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organisme;
use Illuminate\Support\Facades\DB;

class OrganismesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Vider la table avant de la peupler
        Organisme::truncate();
        
        $organismes = [
            [
                'nom_organisme' => 'ENEO',
                'domaine_organisme' => 'Électricité',
                'email_organisme' => 'contact@eneo.cm',
                'tel_organisme' => '+237 2 33 50 11 11',
                'description_organisme' => 'Premier fournisseur d\'électricité au Cameroun',
                'adresse_organisme' => 'Immeuble Eneo, Boulevard du 20 Mai, Yaoundé',
                'statut_organisme' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom_organisme' => 'CAMWATER',
                'domaine_organisme' => 'Eau potable',
                'email_organisme' => 'contact@camwater.cm',
                'tel_organisme' => '+237 2 22 23 00 00',
                'description_organisme' => 'Société de distribution d\'eau potable au Cameroun',
                'adresse_organisme' => 'Rue 1.840, Bastos, Yaoundé',
                'statut_organisme' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom_organisme' => 'CAMRAIL',
                'domaine_organisme' => 'Transport ferroviaire',
                'email_organisme' => 'contact@camrail.net',
                'tel_organisme' => '+237 2 33 42 38 38',
                'description_organisme' => 'Société de chemin de fer du Cameroun',
                'adresse_organisme' => 'Boulevard de la Liberté, Bonapriso, Douala',
                'statut_organisme' => 'actif',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($organismes as $organisme) {
            Organisme::create($organisme);
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
