<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class UserPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        $this->model = 'user';
    }
    /**
     * Determine whether the user can view all non-deleted models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function index(User $user)
    {
        $role = Role::findByName($user->getRoleNames()[0]);
        if ($role->hasPermissionTo($this->model . '-index')) {
            return true;
        }
    }
    /**
     * Determine whether the user can view all (including deleted) models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function indexAll(User $user)
    {
        return $this->canUserWithRolesAccessPermission($user, $this->model . '-indexAll');
    }
    /**
     * Determine whether the user can view all (including deleted) models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function indexTrashed(User $user)
    {
        return $this->canUserWithRolesAccessPermission($user, $this->model . '-indexTrashed');
    }
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function show(User $user, User $model)
    {
      // users can view their own profiles
        if ($user->id == $model->id) {
            return true;
        }

        return $this->canUserWithRolesAccessPermission($user, $this->model . '-show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function store(User $user)
    {
        return $this->canUserWithRolesAccessPermission($user, $this->model . '-store');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function update(User $user): bool
    {
        $routeId = request()->route()->parameters['id'];
        if (!empty($routeId) && $routeId == $user->id) {
            return true;
        }

        return $this->canUserWithRolesAccessPermission($user, $this->model . '-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $this->canUserWithRolesAccessPermission($user, $this->model . '-delete');

        //should users be able to delete their own profiles?
        //return $user->id == $model->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return $this->canUserWithRolesAccessPermission($user, $this->model . '-restore');
    }

    /**
     * Cycles over user roles and returns true if any of them has sufficient permission
     * @param User $user
     * @return bool
     */
    protected function canUserWithRolesAccessPermission(User $user, $permission) {
        foreach($user->getRoleNames() as $roleName) {
            $role = Role::findByName($roleName);
            if ($role->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }
}
