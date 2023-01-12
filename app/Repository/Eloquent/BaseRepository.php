<?php

namespace App\Repository\Eloquent;

use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Traits\ResponseTrait;

class BaseRepository implements EloquentRepositoryInterface
{
    use ResponseTrait;

    protected Model $model;
    protected Request $request;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return false|\Illuminate\Database\Eloquent\Collection|Model[]|mixed
     */
    public function index()
    {
        try {
            return $this->model::all();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

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
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @return false|mixed
     */
    public function indexTrashed()
    {
        try {
            return $this->model::onlyTrashed()->get();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param $id
     * @return false|mixed
     */
    public function show($id)
    {
        try {
            return $this->model::find($id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

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

            return $this->ok(__('general.record.create'), $this->model);
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
    public function update(array $data, $id)
    {
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function delete($id)
    {
        try {
            $collection = $this->model::find($id);
            if ($collection) {
                $collection->delete();

                return $this->ok(__('general.record.destroy.success', ['id' => $id]));
            } else {

                return $this->notFound(__('general.record.not_found', ['id' => $id]));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function restore($id)
    {
        try {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $model->restore();

                return $this->ok(__('general.record.restore.success', ['id' => $id]));
            } else {

                return $this->notFound(__('general.record.not_found', ['id' => $id]));
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return false;
        }
    }
}
