<?php

namespace App\Services;

use App\Models\User;

class UserDataService
{
    public $userData;

    public function __construct()
    {
        $this->userData = [];
    }

    public function hydrateUserWithAdditionalData($userCollection)
    {
        foreach ($userCollection as $user) {
            array_push($this->userData, eavParser($user, 'data'));
        }

        return User::hydrate($this->userData);
    }
}
