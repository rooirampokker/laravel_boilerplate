<?php

use Illuminate\Support\Str;

/**
 * Entity-Attribute-Value parser.
 * Assumes the injected collection has a data relationship defined with key and value attributes
 *
 * @param $collection
 * @return array
 */
if (!function_exists('eavParser')) {
    function eavParser($collection): array
    {
        $dataCollection = [];
        foreach ($collection->data as $data) {
            $dataCollection['data'][$data->key] = $data->value;
        }

        return array_merge($collection->toArray(), $dataCollection);
    }
}

/**
 * Extracts the model name from the route prefix (last path element)
 * Uppercases and singularizes and returns it
 * @param $request
 * @return string
 */
if (!function_exists('getModelNameFromRoute')) {
    function getModelNameFromRoute($request): string
    {
        $routePrefix = $request->route()->getPrefix(); //to be used as model
        $controllerAndMethod = explode("/", $routePrefix);
        $model = ucfirst(end($controllerAndMethod));
        $model = Str::singular($model);
        return "App\Models\\" . $model;
    }
}
