<?php

namespace App\Services;

use App\Models\User;

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
}
