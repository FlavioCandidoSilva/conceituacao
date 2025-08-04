<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'role',
        'description',
        'permissions',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }
        
        return in_array($permission, $this->permissions);
    }

    public function addPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
    }

    public function removePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_diff($permissions, [$permission]);
        $this->permissions = array_values($permissions);
        $this->save();
    }
}
