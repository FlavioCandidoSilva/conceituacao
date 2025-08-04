<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(
            ['role' => 'Administrador'],
            [
                'description' => 'Perfil com acesso total ao sistema',
                'permissions' => [
                    'user.create',
                    'user.edit', 
                    'user.delete',
                    'user.view',
                    'role.create',
                    'role.edit',
                    'role.delete',
                    'role.view'
                ],
                'is_active' => true
            ]
        );

        Role::firstOrCreate(
            ['role' => 'Usuário'],
            [
                'description' => 'Perfil padrão para usuários comuns',
                'permissions' => [
                    'user.view'
                ],
                'is_active' => true
            ]
        );

        Role::firstOrCreate(
            ['role' => 'Gerente'],
            [
                'description' => 'Perfil para gerenciamento de usuários',
                'permissions' => [
                    'user.create',
                    'user.edit',
                    'user.view',
                    'role.view'
                ],
                'is_active' => true
            ]
        );
    }
}
