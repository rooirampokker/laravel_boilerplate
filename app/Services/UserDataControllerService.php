<?php

namespace App\Services;

use App\Models\UserData;
use Illuminate\Support\Arr;

class UserDataControllerService
{
    public function __construct()
    {
    }

    /**
     * @param $user
     * @return array
     */
    public function collapseUserDataIntoParent($user)
    {
        $dataCollection = [];
        foreach ($user->data as $data) {
            $dataCollection[$data->key] = $data->value;
        }
        unset($user->data);

        return array_merge($user->toArray(), $dataCollection);
    }
}
