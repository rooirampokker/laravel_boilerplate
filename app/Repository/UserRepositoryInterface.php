<?php

namespace App\Repository;

use Illuminate\Http\Request;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    public function login(Request $request);
}
