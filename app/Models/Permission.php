<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Permission extends SpatiePermission
{
    use HasFactory;
    use UsesTenantConnection;
}
