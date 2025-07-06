<?php

namespace App\Repository;

use App\Models\Permission;
use App\Models\Role;
use App\Repository\Interfaces\RoleRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed
     */
    public function index()
    {
        try {
            return $this->model::with('permissions')->get();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function show($id)
    {
        try {
            return $this->model::with('permissions')->find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param FormRequest $request
     * @param $id
     * @return array|false|mixed|void
     */
    public function update(FormRequest $request, $id)
    {
        try {
            $thisRole = $this->model::find($id);
            if ($thisRole) {
                $success = $this->model->fill($request->all())->save();

                return $this->ok(__('roles.update.success'), $success);
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param FormRequest $request
     * @return mixed
     */
    public function store(FormRequest $request): mixed
    {
        try {
            $response = $this->model::create($request->all());
            if ($response) {
                return $response;
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

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
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

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
            $role = $this->model::find($role_id);
            if ($role) {
                $collection = $role->revokePermissionTo($permission_id);
                if ($collection) {
                    return $collection;
                }
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
    public function syncPermission($request, $id)
    {
        try {
            $role = $this->model::find($id);
            if ($role) {
                $params     = $request->all();
                $collection = $role->syncPermissions(Permission::whereIn('id', $params['permissions'])->get()->pluck('name'));
                if ($collection) {
                    return $collection;
                }
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
}
