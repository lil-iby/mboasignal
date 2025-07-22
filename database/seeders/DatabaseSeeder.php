<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Désactiver temporairement les contraintes de clé étrangère
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Appeler les seeders dans le bon ordre pour respecter les contraintes de clé étrangère
        $this->call([
            RoleSeeder::class, // Ajout du seeder pour les rôles
            SuperAdminSeeder::class, // Création des super administrateurs
            CategoriesTableSeeder::class,
            OrganismesTableSeeder::class,
            UtilisateursTableSeeder::class,
            SignalementsTableSeeder::class,
            MediaTableSeeder::class,
        ]);

        // Réactiver les contraintes de clé étrangère
        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
