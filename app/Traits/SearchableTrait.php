<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\EavKey;

trait SearchableTrait
{
    protected $searchTerm;
    protected $searchableAttributes;
    protected $searchField;
    protected $model;
    protected $query;

    /**
     * creates a new instance of the current model,
     * searches across all $searchable attributes and
     * returns results as a hydrated list of model instances
     *
     * @param $request
     * @return array|mixed
     */
    public function scopeSearch(Builder $query, $request)
    {
        $this->query = $query;
        $this->searchTerm = $request['search'] ?? null;
        $this->searchField = $request['field'] ?? null;

        $this->model = $this->getModelContext();
        $this->searchableAttributes = $this->getSearchableAttributes();

        try {
            if (isset($request['relation'])) {
                $result = $this->searchRelation($request['relation']);
            } else {
                $result = $this->searchBaseModel();
            }

            return $result;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            return [];
        }
    }

    /**
     * Searches through the specified relation using a sub-select
     *
     * @param $relation
     * @return mixed
     */
    private function searchRelation($relation)
    {
        //this needs to be camelCased to match the relation named in the target model
        $relation = Str::camel($relation);
        $relationInstance = $this->model->$relation()->getRelated();
        $this->searchableAttributes = $relationInstance::$searchable ?? [];
        //was a searchfield specified?
        //is the specified field searchable?
        if (!isset($this->searchField) || in_array($this->searchField, $this->searchableAttributes)) {
            $results = $this->searchRelatedModel($relation);
        } else {
            //specified field was not searchable...
            //check the EAVKey model for any whitelisted keys associated to the current model and search againts that
            $relatedModel = class_basename($relationInstance);
            $eavKeys = EavKey::where('model_type', 'LIKE', '%' . $relatedModel)
                             ->where('key', '=', $this->searchField)->first();
            $results = $this->searchRelatedEAVTable($relatedModel, $eavKeys);
        }

        return $results;
    }

    /**
     * @param $relation
     * @param $eavKeys
     * @return mixed
     */
    private function searchRelatedEAVTable($relation, $eavKeys)
    {
        return $this->model->whereHas($relation, function ($queryBuilder) use ($eavKeys) {
            $queryBuilder->where(function ($query) use ($eavKeys) {
                foreach ($this->searchableAttributes as $attribute) {
                    $query->where($attribute, 'LIKE', '%' . $this->searchTerm . '%')
                          ->where('eav_key_id', $eavKeys->id);
                }
            });
        });
    }

    /**
     * @param $relation
     * @param $relationInstance
     * @return mixed
     */
    private function searchRelatedModel($relation)
    {
        return $this->query->whereHas($relation, function ($queryBuilder) {
            $queryBuilder->where(function ($query) {
                if (isset($this->searchField)) {
                    $query->where($this->searchField, 'LIKE', '%' . $this->searchTerm . '%');
                } else {
                    foreach ($this->searchableAttributes as $attribute) {
                        $query->orWhere($attribute, 'LIKE', '%' . $this->searchTerm . '%');
                    }
                }
            });
        });
    }

    /**
     * @return mixed
     */
    private function searchBaseModel()
    {
        return self::where(function ($queryBuilder) {
            if ($this->searchField) {
                $queryBuilder->where($this->searchField, 'LIKE', '%' . $this->searchTerm . '%');
            } else {
                foreach ($this->searchableAttributes as $attribute) {
                    $queryBuilder->orWhere($attribute, 'LIKE', '%' . $this->searchTerm . '%');
                }
            }
        });
    }

    /**
     * @return array
     */
    public function getSearchableAttributes()
    {
        return $this->model::$searchable ?? [];
    }

    /**
     * Sets $this->model to either the base model or to a specified relation
     *
     * @param Array $params
     * @return mixed
     */
    protected function getModelContext()
    {
        $modelName = get_called_class();
        return new $modelName();
    }
}
