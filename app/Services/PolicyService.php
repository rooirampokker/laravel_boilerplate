<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;

class PolicyService
{
    /**
     * Cycles over user roles and returns true if any of them has sufficient permission
     * @param User $user
     * @return bool
     */
    public function doesUserWithRolesHavePermission(User $user, $permission)
    {
        foreach ($user->getRoleNames() as $roleName) {
            $role = Role::findByName($roleName);
            if ($role->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }
}
