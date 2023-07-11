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
     * @return false|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]|mixed
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
     * @return false|mixed
     */
    public function indexAll()
    {
        try {
            $tenantCollection = $this->model::withTrashed()->get();

            return $tenantCollection;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
    /**
     * @return array|mixed
     */
    public function indexTrashed()
    {
        try {
            $tenantCollection = $this->model::onlyTrashed()->get();

            return $tenantCollection;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param $id
     * @return false|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function show($id)
    {
        try {
            return $this->model::with('domains')->find($id);
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
            $thisTenant = $this->model::find($id);
            if ($thisTenant) {
                $success = $thisTenant->fill($request->all())->update();
                return $this->ok(__('tenants.update.success'), $success);
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
            $tenant = $this->model::create($request->all());
            if ($tenant) {
                $domainName = str_slug($request->name, '_') . "." . env('APP_DOMAIN');
                $tenant->domains()->create(['domain' => $domainName]);
                return $tenant;
            }

            return false;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
}
