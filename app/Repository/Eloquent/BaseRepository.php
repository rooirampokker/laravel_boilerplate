<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class BaseRepository implements EloquentRepositoryInterface
{
    protected Model $model;
    protected Request $request;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed|void
     */
    public function index()
    {
        try {
            return $this->model::all();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }
    /**
     * Fetches all records, including soft-deleted
     * @return mixed|void
     */
    public function indexAll()
    {
        try {
            return $this->model::withTrashed()->get();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }
    /**
     * @return mixed|void
     */
    public function indexTrashed()
    {
        try {
            return $this->model::onlyTrashed()->get();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function show($id)
    {
    }

    /**
     * @param $request
     * @return mixed
     * @throws \Exception
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

            return $this->model;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
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
     * @return mixed
     */
    public function delete($id)
    {
        $success = true;
        try {
            $collection = $this->model::find($id);
            if ($collection) {
                $success = $collection->delete();
            } else {
                throw new \Exception(__('general.record.not_found', ['id' => $id]));
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $success;
    }
    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $success = false;
        try {
            $user = User::withTrashed()->find($id);
            if (!$user) {
                throw new \Exception(__('general.record.not_found', ['id' => $id]));
            }
            $success = $user->restore();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return $success;
    }
}
