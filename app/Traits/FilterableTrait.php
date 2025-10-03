<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\EavKey;

trait FilterableTrait
{
    protected $filterableAttributes;
    protected $models;
    protected $query;
    protected $reservedFieldNamesArray = ['page', 'includes', 'filter_relation', 'search', 'limit'];

    /**
     * @param Builder $query
     * @param $params
     * @return array|void|null
     */
    public function scopeFilterResults(Builder $query, $params)
    {
        $this->models = $this->getFilterModelContext($params);
        $this->filterableAttributes = $this->getFilterableAttributes();
        try {
            $query = $this->filterBaseModel($params, $query);
            $query = $this->filterByRelation($params, $query);

            return $this->orderResults($query, $params);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            return [];
        }
    }

    /**
     * @param $params
     * @return mixed
     */
    private function filterByRelation($params, $query)
    {
        if (isset($params['filter_relation'])) {
            $filteredRelationships = explode(',', $params['filter_relation']);
            foreach ($filteredRelationships as $modelName) {
                $tableName = Str::snake($modelName) . "_";
                $relationshipName = Str::camel($modelName);
                $query->whereHas($relationshipName, function (Builder $subQuery) use ($params, $tableName, $relationshipName) {
                    $modelName = ucfirst(Str::camel(Str::singular($relationshipName)));
                    $related = $subQuery->getModel();
                    //almost exclusively for attendeeActivities
                    if (method_exists($related, 'scopeIsLastActivityByModel')) {
                        $subQuery->isLastActivityByModel();
                    }
                    foreach ($params as $filterField => $filterValue) {
                        if (
                            !in_array($filterField, $this->reservedFieldNamesArray, true) &&
                            str_starts_with($filterField, $tableName)
                        ) {
                            $filterField = str_replace($tableName, '', $filterField);
                            //apply filter if param matches filterable value

                            $searchString = explode(',', $filterValue);
                            if (in_array($filterField, $this->filterableAttributes[$modelName], true)) {
                                $subQuery->whereIn($filterField, $searchString);
                            }
                        }
                    }
                });
            }
        }

        return $query;
    }

    /**
     * A filter could include data from both core and EAV table
     *
     * @param $params
     * @param $query
     * @return mixed
     */
    private function filterBaseModel($params, $query)
    {
        //this should be a limited array of 1 model
        foreach ($this->models as $modelName => $modelObject) {
            //first search the base model, if the field is searchable
            if (array_intersect_key($params, array_flip($this->filterableAttributes[$modelName]))) {
                foreach ($params as $filterField => $filterValue) {
                    //apply filter if param matches filterable value
                    $searchString = explode(',', $filterValue);
                    if (in_array($filterField, $this->filterableAttributes[$modelName])) {
                        $query->whereIn($filterField, $searchString);
                    }
                }
            }
        }

        return $this->filterByDataTable($query, $params);
    }

    /**
     * checks the EAVKey model for any whitelisted keys associated to the current model and search against that
     *
     * @param $query
     * @param $params
     * @return mixed
     */
    private function filterByDataTable($query, $params)
    {
        $relatedModel = get_called_class() . 'Data';
        if (class_exists($relatedModel)) {
            $shortClassName = explode('\\', $relatedModel);
            $shortClassName = end($shortClassName);

            $eavKeys = EavKey::where('model_type', $relatedModel)
                ->whereIn('key', array_keys($params))->get();
            $query = $this->filterRelatedEAVTable($eavKeys, $params, $query) ?? $query;
        }

        return $query;
    }
    /**
     * @param $relation
     * @param $relationInstance
     * @param $eavKeys
     * @return mixed
     */
    private function filterRelatedEAVTable($eavKeys, $params, $query)
    {
        $availableEavKeys = array_flip($eavKeys->pluck('key')->toArray());
        $params = array_intersect_key($params, $availableEavKeys);
        if ($params) {
            return $query->whereHas('data', function ($queryBuilder) use ($eavKeys, $params) {
                $queryBuilder->where(function ($query) use ($eavKeys, $params) {
                    foreach ($params as $filterField => $filterValue) {
                        if (!in_array($filterField, $this->reservedFieldNamesArray)) {
                            $key = $eavKeys->where('key', $filterField)->first();
                            $value = explode(',', $filterValue);
                            $query->whereIn('value', $value)
                                ->where('eav_key_id', $key->id);
                        }
                    }
                });
            });
        }

        return null;
    }

    /**
     * @param $query
     * @param $params
     * @return void
     */
    private function orderResults($query, $params)
    {
        if (isset($params['order_by'])) {
            if (isset($params['order'])) {
                $query->orderBy($params['order_by'], $params['order']);
            } else {
                $query->orderBy($params['order_by'], 'DESC');
            }
        }

        return $query;
    }

    /**
     * gets the $searchable array from the specified class(es) - searchable == filterable
     *
     * @return array
     */
    public function getFilterableAttributes()
    {
        $filterableAttributes = [];
        foreach ($this->models as $modelName => $modelObject) {
            $filterableAttributes[$modelName] = $modelObject::$searchable ?? [];
        }
        return $filterableAttributes;
    }

    /**
     * Sets $this->model to either the base model or to a specified relation
     *
     * @param array $params
     * @return array
     */
    protected function getFilterModelContext(array $params = [])
    {
        $modelArray = [];
        $modelName = get_called_class();
        $modelArray[class_basename($modelName)] = new $modelName();

        if (isset($params['filter_relation'])) {
            $models = explode(',', $params['filter_relation']);
            foreach ($models as $model) {
                //singularize class name, but don't turn Data into Datum
                $modelName = str_ends_with($model, 'Data') ? $model : str_singular($model);
                $modelName = ucfirst(Str::camel($modelName));
                $modelNameSpace = "\App\Models\\" . $modelName;
                $modelArray[$modelName] = new $modelNameSpace();
            }
        }

        return $modelArray;
    }
}
