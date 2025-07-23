<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Utilisateur as User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer les rôles
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'superadmin'],
            [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Créer le rôle citoyen
        $citizenRole = Role::firstOrCreate(
            ['name' => 'citoyen'],
            [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Créer le rôle technicien
        $technicianRole = Role::firstOrCreate(
            ['name' => 'technicien'],
            [
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Créer un utilisateur super admin par défaut
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Attribuer le rôle superadmin à l'utilisateur
        if (!$superAdmin->hasRole('superadmin')) {
            $superAdmin->assignRole('superadmin');
        }

        // Créer un utilisateur admin par défaut
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Attribuer le rôle admin à l'utilisateur
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}
