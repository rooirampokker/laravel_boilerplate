<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Traits\RepositoryResponseTrait;

class BaseRepository implements EloquentRepositoryInterface
{
    use RepositoryResponseTrait;
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

            return $this->ok(__('general.index.success', $this->model::all()));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
    }
    /**
     * Fetches all records, including soft-deleted
     * @return mixed|void
     */
    public function indexAll()
    {
        try {

            return $this->ok(__('general.index.success'), $this->model::withTrashed()->get()->toArray());
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
    }
    /**
     * @return mixed|void
     */
    public function indexTrashed()
    {
        try {

            return $this->ok(__('general.index.success'), $this->model::onlyTrashed()->get()->toArray());
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
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

            return $this->ok(__('users.update.success'), $this->model);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
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

            return $this->exception($exception);
        }
    }
    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->find($id);
            if ($user) {
                $user->restore();

                return $this->ok(__('general.record.restore.success', ['id' => $id]));
            } else {

                return $this->notFound(__('general.record.not_found', ['id' => $id]));
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());

            return $this->exception($exception);
        }
    }
}
