<?php

namespace App\Http\Repository\api\v1\Interfaces;

use Illuminate\Http\Request;

interface PasswordResetRepositoryInterface extends BaseRepositoryInterface
{
    public function create(Request $request);
    public function find($token);
    public function reset(Request $request);
}
