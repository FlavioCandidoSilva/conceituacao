<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
            ]
        );
        $adminRole = Role::where('role', 'Administrador')->first();
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Usuário padrão
        $user = User::firstOrCreate(
            ['email' => 'usuario@example.com'],
            [
                'name' => 'Usuário Comum',
                'password' => Hash::make('usuario123'),
            ]
        );
        $userRole = Role::where('role', 'Usuário')->first();
        if ($userRole) {
            $user->roles()->syncWithoutDetaching([$userRole->id]);
        }

        // Gerente
        $manager = User::firstOrCreate(
            ['email' => 'gerente@example.com'],
            [
                'name' => 'Gerente do Sistema',
                'password' => Hash::make('gerente123'),
            ]
        );
        $managerRole = Role::where('role', 'Gerente')->first();
        if ($managerRole) {
            $manager->roles()->syncWithoutDetaching([$managerRole->id]);
        }
    }
}
