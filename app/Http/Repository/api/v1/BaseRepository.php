<?php

namespace App\Http\Repository\api\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Http\Repository\api\v1\Interfaces\BaseRepositoryInterface;
use App\Traits\ResponseTrait;
use App\Services\DataService;

class BaseRepository implements BaseRepositoryInterface
{
    use ResponseTrait;
    protected DataService $dataService;
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->dataService = new DataService();
    }

    /**
     * @param $request
     * @return array|false|mixed
     */
    public function index($request)
    {
        try {
            $limit = $request['limit'] ?? null;
            $trashed = $request['trashed'] ?? null;
            $search = $request['search'] ?? null;
            $relationshipList = $this->dataService->buildRelationshipListForEagerLoading($this->model, $request);

            $collection = $this->model
                ->with($relationshipList)
                ->when(($search), function ($query) use ($request) {
                    return $query->search($request);
                })
                ->when(($trashed), function ($query) {
                    return $query->onlyTrashed();
                })
                ->paginate($limit);

            return paginateCollection($collection);

        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * Fetches all records, including soft-deleted
     *
     * @return false|mixed
     */
    public function indexAll()
    {
        try {
            return $this->model::withTrashed()->get();
        } catch (\Exception $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $id
     * @return false|\Illuminate\Support\Collection|mixed
     */
    public function show($id)
    {
        try {
            //prevents N+1 query when outputting the eav-keyed 'data' items with eager-loading
            $includedRelationships = method_exists($this->model, 'data') ? ['data'] : [];
            $model = $this->model->with($includedRelationships)->find($id);
            if (!empty($model)) {
                return collect([$model]);
            }
            return false;

        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $request
     * @return mixed
     */
    public function store($request): mixed
    {
        try {
            $data = $request->all();
            foreach ($data as $key => $value) {
                //complex data types should be saved in the model-specific repository
                if (!is_array($value)) {
                    $this->model->$key = $value;
                }
            }
            $this->model->save();
            return collect([$this->model]);
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $request
     * @param $id
     * @return false|\Illuminate\Support\Collection|mixed
     */
    public function update($request, $id)
    {
        try {
            $record = $this->model->find($id);
            if ($record) {
                $update = $record->fill($request->all())->save();

                if ($update) {
                    $model = $record->fresh();
                    return collect([$model]);
                }
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $id
     * @return false|mixed
     */
    public function delete($id)
    {
        try {
            $collection = $this->model::find($id);
            if ($collection) {
                return $collection->delete();
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function restore($id)
    {
        try {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $model->restore();
                return true;
            }

            return false;
        } catch (\Throwable $exception) {
            $this->logError($exception);
            return false;
        }
    }
}
