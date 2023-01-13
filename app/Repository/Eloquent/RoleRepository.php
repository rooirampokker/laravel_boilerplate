<?php

namespace App\Repository\Eloquent;

use App\Repository\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }
}
