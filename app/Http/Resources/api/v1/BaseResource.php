<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\ResourceRouterService;
use Illuminate\Support\Str;

class BaseResource extends JsonResource
{
    protected ResourceRouterService $router;
    protected array $controllerNameAndActionArray;
    protected object $request;
    protected string $modelName;
    protected string $cacheKey;
    protected array $visitedRelations = [];

    public function __construct($resource)
    {
        parent::__construct($resource);

        if (!empty($resource)) {
            $this->resource = $resource;
            $this->modelName = class_basename($resource);
            $this->request = request();
            //we might not be logging in as a user with username and password, but rather as an app
            $userId = ($this->request->user() !== null) ? $this->request->user()->id : 'app-key';
            $this->cacheKey = $this->request->getHttpHost() . '-' . $this->modelName . '-' . $userId . '-resource:' . $this->resource->id;

            $this->controllerNameAndActionArray = getControllerNameAndAction($this->request);
            $this->router = new ResourceRouterService($this->modelName, $this->request);
        }
    }

    /**
     * used in $this->setResource
     * flags relations a visited to prevent recursion when including resources that reference each other
     *
     * @param array $visited
     * @return $this
     */
    public function withVisitedRelations(array $visited): static
    {
        $this->visitedRelations = $visited;
        return $this;
    }

    /**
     * Return dedicated resource with all details for 'show' endpoints
     * All other endpoints only get the ID
     *
     * Business rules dictate that this method is only intended for many-many relationships
     * Any other relationship is simply added directly in the relevant Resource class
     *
     * @param $resource
     * @param $debug
     * @return array|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|mixed
     */
    protected function setResource($baseClass, $relationName)
    {
        $resource = $baseClass->$relationName;

        if (!$resource) {
            return [];
        }

        // skip to prevent recursion if this relation already features in the response
        if (in_array($relationName, $this->visitedRelations, true)) {
            return [];
        }

        // Mark this relation as visited
        $newVisited = array_merge($this->visitedRelations, [$relationName]);
        $relatedModelClass = get_class($baseClass->$relationName()->getRelated());
        $modelResource = __NAMESPACE__ . "\\" . class_basename($relatedModelClass) . "Resource";
        if (is_iterable($resource)) {
            return collect($resource)->map(function ($item) use ($modelResource, $newVisited) {
                return (new $modelResource($item))->withVisitedRelations($newVisited);
            });
        } else {
            return (new $modelResource($resource))->withVisitedRelations($newVisited);
        }
    }

    /**
     * @param $baseClass
     * @param $relationName
     * @param $resourceClass
     * @return mixed|void|null
     */
    protected function setCustomResource($baseClass, $relationName, $resourceClass)
    {
        $resource = false;
        if ($relationName) {
            $resource = $baseClass->$relationName;
        }
        if ($resource) {
            $modelResource = __NAMESPACE__ . "\\" . $resourceClass;
            //for a collection of items...
            if (is_iterable($resource)) {
                return $resource ? $modelResource::collection($resource) : null;
            }
            //for single item...
            return $modelResource::make($resource);
        }

        return [];
    }

    /**
     * Combines all elements of the Resource object to be served
     *
     * @param $coreModel
     * @param $relations
     * @return array
     */
    protected function composeResourceReturnArray($coreModel, $relations)
    {
        //prevents the count parameter from affecting included resources by checking...
        //...does the current model resource being rendered here feature in the 'count'? if so, don't show the nested count
        $thisClassName = Str::plural(Str::snake(class_basename($this->modelName)));

        if (isset($this->request['count']) && (!strstr($thisClassName, $this->request['count']))) {
            $relations['count'] = $this->relationAggregator();
        }
        $this->router = new ResourceRouterService($this->modelName, $this->request);

        $relationList = [
            'relation_list' => $this->when($this->router->allowedActions(
                ['show'],
                'relation_list',
            ), array_keys($relations))
        ];

        //only remove where null - we still want to retain empty arrays
        $relations = array_filter($relations, function ($val) {
            return $val !== null;
        });
        return array_merge($coreModel, $relations, $relationList);
    }

    /**
     * @return array
     */
    protected function relationAggregator()
    {
        $counts = [];
        $baseClass = $this->resource;
        $relationships = explode(',', $this->request['count']);
        foreach ($relationships as $relationship) {
                $camelCasedRelationship = Str::camel($relationship);
            if ($baseClass->$camelCasedRelationship) {
                //some relations return as arrays or as objects - cater for both
                $counts[$relationship] = is_array($baseClass->$camelCasedRelationship) ? count($baseClass->$camelCasedRelationship) : $baseClass->$camelCasedRelationship->count();
            }
        }

        return $counts;
    }

    protected function hydrateData($request, $data)
    {
        //only process if data isn't already pre-formatted into an associative array via the hydrateNestedCollectionWithAdditionalData method
        if (is_object($data)) {
            $formattedData = [];
            $dataCollection = DataResource::collection($data)->toArray($request);
            foreach ($dataCollection as $item) {
                $formattedData = array_merge($formattedData, $item);
            }
            //either returns content, or an empty object - prevents parsing issues of [] vs {}
            return count($formattedData) ? $formattedData : (object)[];
        }

        return $data;
    }
}
