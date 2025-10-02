<?php

namespace App\Http\Repository\api\v1\Interfaces;

use Illuminate\Http\Request;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function login(Request $request);
}
