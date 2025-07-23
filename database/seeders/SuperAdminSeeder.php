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
                'nom_utilisateur' => 'Admin',
                'prenom_utilisateur' => 'System',
                'email_utilisateur' => 'admin@mboasignal.com',
                'pass_utilisateur' => Hash::make('admin123'),
                'tel_utilisateur' => '+237690000000',
                'type_utilisateur' => 'superadmin',
                'etat_compte' => 'activé',
                'statut_en_ligne' => false,
                'derniere_connexion' => now(),
                'date_inscription' => now(),
                'derniere_modification' => now(),
                'email_verified_at' => now(),
            ],
            [
                'nom_utilisateur' => 'Super',
                'prenom_utilisateur' => 'Admin',
                'email_utilisateur' => 'superadmin@mboasignal.com',
                'pass_utilisateur' => Hash::make('superadmin123'),
                'tel_utilisateur' => '+237690000001',
                'type_utilisateur' => 'superadmin',
                'etat_compte' => 'activé',
                'statut_en_ligne' => false,
                'derniere_connexion' => now(),
                'date_inscription' => now(),
                'derniere_modification' => now(),
                'email_verified_at' => now(),
            ]
        ];

        foreach ($superAdmins as $admin) {
            $user = User::firstOrCreate(
                ['email_utilisateur' => $admin['email_utilisateur']],
                $admin
            );

            // Assigner le rôle Super Admin
            $user->assignRole($superAdminRole);
        }

        $this->command->info('Super administrateurs créés avec succès !');
    }
}
