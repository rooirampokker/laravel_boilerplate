<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo('list users', 'api')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        // users can view their own profiles
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->hasPermissionTo('create users', 'api')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {

        if ($user->hasPermissionTo('update users', 'api')) {
            return true;
        }

        //users should be able to update their own profiles
        return $user->id == $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function destroy(User $user, User $model)
    {
        if ($user->hasPermissionTo('delete users', 'api')) {
            return true;
        }

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
        if ($user->hasPermissionTo('restore users', 'api')) {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return false;
    }
}
