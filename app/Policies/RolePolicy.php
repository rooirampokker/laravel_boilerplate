<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Services\PolicyService;

class RolePolicy
{
    use HandlesAuthorization;

    private string $model;
    private PolicyService $policyService;

    public function __construct()
    {
        $this->model = 'role';
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
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return mixed
     */
    public function show(User $user, Role $model)
    {
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
     * @param  \App\Models\Role $model
     * @return mixed
     */
    public function update(User $user, Role $model): bool
    {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Role $model
     * @return mixed
     */
    public function delete(User $user, Role $model)
    {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-delete');
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function assignPermissions(User $user, Role $model) {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-assignPermissions');
    }

    /**
     * @param User $user
     * @param Role $model
     * @return bool
     */
    public function revokePermissions(User $user, Role $model) {
        return $this->policyService->doesUserWithRolesHavePermission($user, $this->model . '-revokePermissions');
    }
}
