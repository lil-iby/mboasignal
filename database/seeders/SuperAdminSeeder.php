<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utilisateur as User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utiliser le guard 'web' qui est le guard par défaut pour l'interface d'administration
        $guardName = 'web';
        
        // Créer les permissions si elles n'existent pas
        $permissions = [
            'gérer utilisateurs',
            'gérer rôles',
            'gérer signalements',
            'voir tableau de bord',
            'exporter données'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName
            ]);
        }

        // Créer le rôle Super Admin s'il n'existe pas
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => $guardName
        ]);
        
        // Donner toutes les permissions au rôle Super Admin
        $superAdminRole->syncPermissions(Permission::where('guard_name', $guardName)->get());

        // Créer les utilisateurs super admin
        $superAdmins = [
            [
                'nom_utilisateur' => 'Yamba',
                'prenom_utilisateur' => 'Erinn',
                'email_utilisateur' => 'yamba@gmail.com',
                'pass_utilisateur' => Hash::make('123456789'),
                'type_utilisateur' => 'superadmin',
                'date_inscription' => now(),
                'etat_compte' => 'activé',
                'date_confirmation' => now(),
                'statut_utilisateur' => 'actif'
            ],
            [
                'nom_utilisateur' => 'Yamba',
                'prenom_utilisateur' => 'Erinn',
                'email_utilisateur' => 'yamba.e@gmail.com',
                'pass_utilisateur' => Hash::make('123456789'),
                'type_utilisateur' => 'superadmin',
                'date_inscription' => now(),
                'etat_compte' => 'activé',
                'date_confirmation' => now(),
                'statut_utilisateur' => 'actif'
            ]
        ];

        foreach ($superAdmins as $admin) {
            $user = User::firstOrCreate(
                ['email_utilisateur' => $admin['email_utilisateur']],
                [
                    'nom_utilisateur' => $admin['nom_utilisateur'],
                    'prenom_utilisateur' => $admin['prenom_utilisateur'],
                    'pass_utilisateur' => $admin['pass_utilisateur'],
                    'type_utilisateur' => $admin['type_utilisateur'],
                    'date_inscription' => $admin['date_inscription'],
                    'etat_compte' => $admin['etat_compte'],
                    'date_confirmation' => $admin['date_confirmation'],
                    'statut_utilisateur' => $admin['statut_utilisateur']
                ]
            );

            // Assigner le rôle Super Admin
            $user->assignRole($superAdminRole);
        }

        $this->command->info('Super administrateurs créés avec succès !');
    }
}
