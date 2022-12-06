<?php

namespace App\Repository;

interface EloquentRepositoryInterface
{
    /**
     * Get all untrashed models
     *
     * @return mixed
     */
    public function all();
    /**
     * Get all trashed models
     *
     * @return mixed
     */
    public function allTrashed();

    /**
     * Show model by id
     *
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * Store a new model
     *
     * @param array $data
     * @return mixed
     */
    public function store(array $data);

    /**
     * Update an existing model
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * $delete an existing model
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);
}
