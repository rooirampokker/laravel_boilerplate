<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class DataService
{
    public $userData;

    public function __construct()
    {
        $this->userData = [];
    }

    public function hydrateCollectionWithAdditionalData($userCollection)
    {
        foreach ($userCollection as $user) {
            array_push($this->userData, eavParser($user, 'data'));
        }

        return User::hydrate($this->userData);
    }

    /**
     * Used to do eager-loading for indexes, which saves database from N+1 issues in the Resource classes
     *
     * @param $model
     * @param $request
     * @return array
     */
    public function buildRelationshipListForEagerLoading($model, $request)
    {
        $includedRelationships = [];
        if (isset($request['includes'])) {
            foreach (explode(",", $request['includes']) as $relationship) {
                //this could be a dot-notated nested relationship request - just add it in that case
                if (!str_contains($relationship, '.')) {
                    $relationship = Str::camel($relationship);
                    if (method_exists($model, $relationship)) {
                        $includedRelationships[] = $relationship;
                    }
                } else {
                    $includedRelationships[] = $relationship;
                }
            }
        }

        return $includedRelationships;
    }
}
