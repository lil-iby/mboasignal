<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nom_categorie' => 'Infrastructure',
                'description_categorie' => 'Problèmes liés aux routes, ponts, bâtiments et autres infrastructures publiques',
            ],
            [
                'nom_categorie' => 'Propreté',
                'description_categorie' => 'Déchets, encombrants, dépôts sauvages et problèmes de propreté urbaine',
            ],
            [
                'nom_categorie' => 'Voirie',
                'description_categorie' => 'Nids-de-poule, éclairage public, signalisation routière et problèmes de circulation',
            ],
            [
                'nom_categorie' => 'Espaces Verts',
                'description_categorie' => 'Entretien des parcs, arbres, pelouses et espaces verts publics',
            ]
        ];

        foreach ($categories as $categorie) {
            Categorie::create($categorie);
        }
    }
}
