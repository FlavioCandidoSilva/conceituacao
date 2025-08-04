<?php 

namespace App\Helpers;

use App\Models\Role;

class RoleHelper
{
    public static function getIdByName(string $roleName): ?int
    {
        return Role::where('role', $roleName)->value('id');
    }
}
