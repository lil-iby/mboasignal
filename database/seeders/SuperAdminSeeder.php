<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
        // Utiliser le bon guard (celui défini dans config/auth.php pour l'API)
        $guardName = config('auth.defaults.guard');
        
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
        $superAdminRole->syncPermissions(Permission::all());

        // Créer les utilisateurs super admin
        $superAdmins = [
            [
                'name' => 'Erinn',
                'email' => 'erinn@admin.com',
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Yamba',
                'email' => 'yamba@admin.com',
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ]
        ];

        foreach ($superAdmins as $admin) {
            $user = User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => $admin['password'],
                    'email_verified_at' => $admin['email_verified_at']
                ]
            );

            // Assigner le rôle Super Admin
            $user->assignRole($superAdminRole);
        }

        $this->command->info('Super administrateurs créés avec succès !');
    }
}
