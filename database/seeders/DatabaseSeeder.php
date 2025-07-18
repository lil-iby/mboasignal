<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'nom_utilisateur' => 'Test User',
            'email_utilisateur' => 'test@example.com',
        ]);

        // Ajouter les catégories d'incidents
        $this->call([
            CategoriesTableSeeder::class,
        ]);
    }
}
