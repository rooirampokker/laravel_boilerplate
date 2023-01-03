<?php

namespace App\Repository;

use Illuminate\Http\Request;

interface PasswordResetRepositoryInterface extends EloquentRepositoryInterface
{
    public function create(Request $request);
    public function find($token);
    public function reset(Request $request);
}
