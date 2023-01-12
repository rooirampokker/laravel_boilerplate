<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Arr;

class UserDataControllerService
{
    public $userData;

    public function __construct()
    {
        $this->userData = [];
    }

    public function hydrateUserWithAdditionalData($userCollection, $additionalDataKey) {
        foreach ($userCollection as $user) {
            array_push($this->userData, eavParser($user, $additionalDataKey));
        }
        return User::hydrate($this->userData);
    }
}
