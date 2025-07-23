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
                'code_utilisateur' => 'SUPERADMIN1',
                'nom_utilisateur' => 'Yamba',
                'prenom_utilisateur' => 'Erinn',
                'email_utilisateur' => 'yamba@gmail.com',
                'pass_utilisateur' => Hash::make('123456789'),
                'type_utilisateur' => 'superadmin',
                'tel_utilisateur' => '+237690000001',
                'etat_compte' => 'activé',
                'type_compte' => 'superadmin',
                'date_inscription' => now(),
                'date_confirmation' => now(),
                'derniere_modification' => now(),
                'statut_en_ligne' => true
            ],
            [
                'code_utilisateur' => 'SUPERADMIN2',
                'nom_utilisateur' => 'Yamba',
                'prenom_utilisateur' => 'Erinn',
                'email_utilisateur' => 'yamba.e@gmail.com',
                'pass_utilisateur' => Hash::make('123456789'),
                'type_utilisateur' => 'superadmin',
                'tel_utilisateur' => '+237690000002',
                'etat_compte' => 'activé',
                'type_compte' => 'superadmin',
                'date_inscription' => now(),
                'date_confirmation' => now(),
                'derniere_modification' => now(),
                'statut_en_ligne' => true
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
