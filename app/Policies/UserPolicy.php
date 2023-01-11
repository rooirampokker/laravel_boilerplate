<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Services\PolicyService;

class UserPolicy
{
    use HandlesAuthorization;
    private string $model;
    private PolicyService $policyService;

    public function __construct()
    {
        $this->model = 'user';
        $this->policyService = new PolicyService();

    }
    /**
     * Determine whether the user can view all non-deleted models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function index(User $user)
    {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-index');
    }
    /**
     * Determine whether the user can view all (including deleted) models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function indexAll(User $user)
    {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-indexAll');
    }
    /**
     * Determine whether the user can view all (including deleted) models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function indexTrashed(User $user)
    {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-indexTrashed');
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

        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function store(User $user)
    {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-store');
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

        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-update');
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
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-delete');

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
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-restore');
    }
}
