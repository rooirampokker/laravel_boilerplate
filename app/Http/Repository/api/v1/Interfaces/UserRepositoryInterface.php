<?php

namespace App\Http\Repository\api\v1\Interfaces;

use Illuminate\Http\Request;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    public function login(Request $request);
}
