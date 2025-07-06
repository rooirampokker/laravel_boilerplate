<?php

namespace App\Repository\Interfaces;

use Illuminate\Http\Request;

interface PasswordResetRepositoryInterface extends EloquentRepositoryInterface
{
    public function create(Request $request);
    public function find($token);
    public function reset(Request $request);
}
