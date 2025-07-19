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
                'nom_organisme' => 'Mairie de Paris',
                'adresse_organisme' => '5 rue de Lobau, 75004 Paris',
                'contact_organisme' => '0142465757 - contact@paris.fr',
            ],
            [
                'nom_organisme' => 'RATP',
                'adresse_organisme' => '54 quai de la Rapée, 75012 Paris',
                'contact_organisme' => '0821104444 - service-client@ratp.fr',
            ],
            [
                'nom_organisme' => 'EDF Paris',
                'adresse_organisme' => '22-30 avenue de Wagram, 75008 Paris',
                'contact_organisme' => '0970697979 - service-client@edf.fr',
            ]
        ];

        foreach ($organismes as $organisme) {
            Organisme::create($organisme);
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
