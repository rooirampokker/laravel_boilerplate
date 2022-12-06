<?php

namespace App\Repository\Eloquent;

use App\Repository\EloquentRepositoryInterface;

class BaseRepository implements EloquentRepositoryInterface
{
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    /**
     * @return mixed|void
     */
    public function all() {

    }

    /**
     * @return mixed|void
     */
    public function allTrashed() {

    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function show($id) {

    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function store(array $data) {

    }

    /**
     * @param array $data
     * @param $id
     * @return mixed|void
     */
    public function update(array $data, $id) {

    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function delete($id) {

    }
}
