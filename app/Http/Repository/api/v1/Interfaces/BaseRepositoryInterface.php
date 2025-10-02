<?php

namespace App\Http\Repository\api\v1\Interfaces;

use App\Repository\Request;
use Illuminate\Foundation\Http\FormRequest;

interface BaseRepositoryInterface
{
    /**
     * Get all untrashed models
     *
     * @return mixed
     */
    public function index($request);

    /**
     * Get all trashed models
     *
     * @return mixed
     */
    public function indexAll();
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
     * @param Request $request
     * @return mixed
     */
    public function store(FormRequest $request);

    /**
     * Update an existing model
     *
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(FormRequest $data, $id);

    /**
     * $delete an existing model
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);
}
