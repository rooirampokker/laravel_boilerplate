<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait ControllerTrait
{
    protected $parentModelKey;
    protected $controllerNameAndActionArray;
    protected $singularCaseActions = [
        'show',
        'store',
        'update',
        'detach',
        'cloneAndUpdate',
    ];

    /**
     * Small utility function - to be called from the extending controller constructor
     *
     * @return array|false|string|string[]
     */
    protected function setModelAndRepository()
    {
        $this->model = class_exists($this->modelPath) ? new $this->modelPath() : $this->modelName;
        //injects 'model' dependency by passing it as an associative array
        $this->repository = app()->makeWith($this->repositoryPath, [
            'model' => $this->model,
        ]);
        $this->language = Str::plural(lcfirst($this->modelName));
    }

    /**
     * pass any included relations through its respective resources, add to the collections array & include pagination
     *
     * @param $response
     * @param $parentResource
     * @return array|bool
     */
    public function formatCollectionRelations($response, $request, $parentModel)
    {
        $this->parentModelKey = $this->getParentModelKey($parentModel);
        $this->controllerNameAndActionArray = getControllerNameAndAction(request());
        $responseCollection = $this->getResponseCollection($response);

        if (!empty($responseCollection)) {
            $parentResource = 'App\Http\Resources\\' . getApiVersionFromUrl() . '\\' . class_basename($parentModel) . "Resource";
            $collection[$this->parentModelKey] = $parentResource::collection($responseCollection);
            $collection = $this->updateResponseForShow($collection);

            if (isset($response['pagination'])) {
                $collection['pagination'] = $response['pagination'];
            }

            if (isset($response['cursor_pagination'])) {
                $collection['cursor_pagination'] = $response['cursor_pagination'];
            }

            return $collection;
        }

        return false;
    }

    /**
     * @param $response
     * @return mixed
     */
    private function getResponseCollection($response)
    {
        return is_array($response) ? $response['collection'] : $response;
    }

    /**
     * Show method returns as a singularly named item. Array wrapping is stripped
     *
     * @param $responseObject
     * @return mixed
     */
    private function updateResponseForShow($responseObject)
    {
        if (in_array($this->controllerNameAndActionArray[1], $this->singularCaseActions)) {
            $singularModelKey = Str::singular($this->parentModelKey);
            $responseObject[$singularModelKey] = $responseObject[$this->parentModelKey][0];
            unset($responseObject[$this->parentModelKey]);
            $this->parentModelKey = $singularModelKey;
        }
        return $responseObject;
    }

    /**
     * Typically used to set the element key on the json respons
     * called from $this->formatCollectionRelations
     *
     * @param $parentModel
     * @return string
     */
    private function getParentModelKey($parentModel)
    {
        $parentModelName = Str::plural(class_basename($parentModel));

        return strtolower(Str::snake($parentModelName));
    }

    /**
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function processStoreResponse($response)
    {
        if ($response) {
            $collection = $this->formatCollectionRelations($response, $this->request, new $this->model());
            return response()->json($this->ok(__(
                $this->language . '.store.success'
            ), $collection));
        }

        // @codeCoverageIgnoreStart
        $responseMessage = $this->error(__(
            $this->language . '.store.failed'
        ));
        return response()->json($responseMessage, $responseMessage['code']);
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function processUpdateResponse($response)
    {
        if ($response) {
            $collection = $this->formatCollectionRelations($response, $this->request, new $this->model());

            return response()->json($this->ok(__(
                $this->language . '.update.success'
            ), $collection));
        }

        // @codeCoverageIgnoreStart
        $responseMessage = $this->error(__(
            $this->language . '.update.failed'
        ));
        return response()->json($responseMessage, $responseMessage['code']);
        // @codeCoverageIgnoreEnd
    }
}
