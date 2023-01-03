<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function __construct()
    {
        $this->model = 'user';
    }
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        if ($user->hasPermissionTo($this->model . '-index')) {
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
        if ($user->hasPermissionTo($this->model . '-store')) {
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
    public function update(User $user): bool
    {
        $routeId = request()->route()->parameters['id'];
        if (!empty($routeId) && $routeId == $user->id) {
            return true;
        }

        return $user->hasPermissionTo($this->model . '-update');
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
        if ($user->hasPermissionTo($this->model . '-delete')) {
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
        if ($user->hasPermissionTo($this->model . '-restore')) {
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
