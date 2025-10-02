<?php

namespace App\Http\Repository\api\v1;

use App\Http\Repository\api\v1\Interfaces\RoleRepositoryInterface;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    /**
     * @param FormRequest $request
     * @return mixed
     */
    public function store($request): mixed
    {
        try {
            $response = $this->model::create($request->all());
            if ($response) {
                return collect([$response]);
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $request
     * @param $id
     * @return false
     */
    public function addPermission($request, $id)
    {
        try {
            $role = $this->model::find($id);
            if ($role) {
                $params     = $request->all();
                $collection = $role->givePermissionTo(Permission::whereIn('id', $params['permissions'])->get()->pluck('name'));
                if ($collection) {
                    return $collection;
                }
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $role_id
     * @param $permission_id
     * @return false
     */
    public function revokePermission($role_id, $permission_id)
    {
        try {
            //get the role and its permissions, where the permissions id matches $permission_id -
            // should only even return a single role with a single permission
            $role = $this->model::with('permissions')->whereHas('permissions', function($query) use($permission_id) {
                $query->where('id', $permission_id);
            })->find($role_id);
            if ($role) {
                $collection = $role->revokePermissionTo($role->permissions->first());

                if ($collection) {
                    return [$collection];
                }
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $request
     * @param $id
     * @return false
     */
    public function syncPermission($request, $id)
    {
        try {
            $role = $this->model::find($id);

            if ($role) {
                $params     = $request->all();
                $collection = $role->syncPermissions(Permission::whereIn('id', $params['permissions'])->get()->pluck('name'));

                if ($collection) {
                    return [$collection];
                }
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }
}
