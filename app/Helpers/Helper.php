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
    function eavParser($collection, $key): array
    {
        $dataCollection = [];
        foreach ($collection->$key as $data) {
            $dataCollection[$key][$data->key] = $data->value;
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
        //we can simply add additional api versions here as they become available
        $availableAPIVersions = ['v1', 'v2'];
        $routePrefix = $request->route()->getPrefix(); //to be used as model

        $controllerAndMethod = array_diff(
            explode("/", $routePrefix),
            array_merge(['api'], $availableAPIVersions)
        );

        //removes empty array elements that might have crept in
        // with the array_diff above if there are double slashes in the URL
        $controllerAndMethod =  array_filter($controllerAndMethod);
        $model = ucfirst(array_shift($controllerAndMethod));
        //removes hyphens from multi-word models, eg attendee-types
        $model = str_replace('-', '', ucwords($model, '-'));
        $model = Str::singular($model);

        return "App\Models\\" . $model;
    }
}
