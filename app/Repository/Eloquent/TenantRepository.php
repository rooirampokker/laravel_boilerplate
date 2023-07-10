<?php

namespace App\Repository\Eloquent;

use App\Repository\TenantRepositoryInterface;
use App\Models\Tenant;
use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        $this->model = $model;
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed
     */
    public function index()
    {
        try {
            return $this->model->get();
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
}
