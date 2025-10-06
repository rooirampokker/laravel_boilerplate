<?php

namespace App\Services;

use Illuminate\Support\Str;

class ResourceRouterService
{
    /*
        Root Resource - the main resource for a model
        (eg. /events endpoint has Event Model as Root Resource)

        Relation Resource - additional resources attached via a relation
        (eg. /events endpoint has Form Model child as Relation Resource)

        Target Resource - the resource in question to be validated whether it is Root Resource or Relation Resource

        rootResourceShort - shorthand name of Resource
        (eg. 'Event' becomes 'App\Http\Controllers\api\v1\EventController' when fully built)
    */

    public $rootResource;
    public $targetResource;
    public $targetAction;

    public $request;

    protected $path = 'App\Http\Controllers\api\v1\\';

    /**
     * @param $rootResourceShort
     * @param $request
     */
    public function __construct($rootResourceShort, $request)
    {
        $this->request = $request;
        $controllerAndAction = getControllerNameAndAction($request);
        $this->rootResource = $this->buildResourcePath($rootResourceShort);
        $this->targetResource = class_basename($controllerAndAction[0]);
        $this->targetAction = $controllerAndAction[1];
    }

    /**
     * Shows node for Root Resource; ignores node for Relation Resource
     *
     * Checks if an 'includes' parameter was passed - If so, compare against current relation...
     * ...and return true/false to specify whether it should be included in response
     * index endpoints should only return core data by default
     * show endpoints should return core data and all relations by default, unless...
     *  ...includes are specified
     *
     * @param array $actions
     * @param $relation
     * @return bool
     */
    public function allowedActions(array $actions, $relation = false)
    {
        $isIncluded = false;
        $request = $this->request->query();
        //if includes are specified, only include those relations and the relation_list
        if (isset($request['includes'])) {
            $includes = explode(',', $request['includes']);
            $isIncluded = $this->includeRelationships($includes, $relation);
            //else include all relations that are authorised/mentioned in the list of allowed $actions
        } elseif (in_array($this->targetAction, $actions, true)) {
            $isIncluded = true;
        }

        return $isIncluded;
    }

    /**
     * caters for nested relationships defined by dot-notation, eg: emails.batchEmails
     * returns true if $relation ('emails') features in $includes (['emails.batchEmails'])
     *
     * @param $haystack
     * @param $needle
     * @return bool
     */
    private function includeRelationships($haystack, $needle): bool
    {
        return collect($haystack)
            ->flatMap(fn($item) => explode('.', $item))
            ->map(fn($segment) => Str::snake($segment))
            ->contains(Str::snake($needle));
    }

    /**
     * Shows node for Relation Resource; ignores node for Root Resource
     *
     * @param $actions
     * @param $relationResourceShort
     * @return bool
     */
    public function allowedActionsForRelationResource($actions, $relationResourceShort)
    {
        $relationResource = $this->buildResourcePath($relationResourceShort);
        $withinSecondaryScope = $this->targetResource === $relationResource;
        return in_array($this->targetAction, $actions, true) && $withinSecondaryScope;
    }

    /**
     * @param $resourceShort
     * @return string
     */
    private function buildResourcePath($resourceShort)
    {
        return $resourceShort . 'Controller';
    }
}
