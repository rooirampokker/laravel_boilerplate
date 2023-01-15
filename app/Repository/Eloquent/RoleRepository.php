<?php

namespace App\Repository\Eloquent;

use App\Repository\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        try {
            $response = $this->model::with('permissions')->get();

            return $response;
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
            $response = $this->model::with('permissions')->find($id);

            return $response;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    public function update($request, $id) {

    }

    public function store($request) {
        
    }
}
