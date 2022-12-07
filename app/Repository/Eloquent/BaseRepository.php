<?php

namespace App\Repository\Eloquent;

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
     * @return mixed|void
     */
    public function indexTrashed()
    {
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
     * @return mixed|void
     */
    public function delete($id)
    {
    }
}
