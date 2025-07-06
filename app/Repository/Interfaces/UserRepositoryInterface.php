<?php

namespace App\Repository\Interfaces;

use Illuminate\Http\Request;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    public function login(Request $request);
}
