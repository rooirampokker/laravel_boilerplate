<?php

namespace App\Repository\Eloquent;

use App\Repository\RoleRepositoryInterface;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Support\Facades\Log;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    private RoleService $roleService;

    public function __construct(Role $model)
    {
        $this->model = $model;
        $this->roleService = new RoleService();
    }

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
     * @param array $data
     * @param $id
     * @return mixed|void
     */
    public function update($request, $id)
    {
        try {
            $thisRole = $this->model::find($id);

            $this->roleService->validateInput($request);
            if ($thisRole) {
                $success = $this->model->fill($request->all())->save();
                return $this->ok(__('roles.update.success'), $success);
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param $request
     * @return array|false|mixed
     */
    public function store($request): mixed
    {
        try {

            $this->roleService->validateInput($request);

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

    public function assignPermissions (Request $request, $id) {

    }

    public function revokePermissions (Request $request, $id) {

    }
}
