<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Media;
use Illuminate\Support\Facades\DB;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Désactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Vider la table avant de la peupler
        DB::table('media')->truncate();
        
        $medias = [
            [
                'fichier' => 'nid_poule_1.jpg',
                'signalement_id' => 1, // Nid de poule
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fichier' => 'nid_poule_2.jpg',
                'signalement_id' => 1, // Nid de poule
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fichier' => 'poubelle_monceau.jpg',
                'signalement_id' => 2, // Poubelle pleine
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'fichier' => 'feu_defectueux.jpg',
                'signalement_id' => 3, // Feu défectueux
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($medias as $media) {
            Media::create($media);
        }
        
        // Réactiver les contraintes de clé étrangère
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
