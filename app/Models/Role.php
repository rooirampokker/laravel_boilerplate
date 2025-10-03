<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

use App\Traits\FilterableTrait;
use App\Traits\SearchableTrait;

class Role extends SpatieRole
{
    use HasFactory;
    use SearchableTrait;
    use FilterableTrait;

    protected $fillable = [
        'name',
        'guard_name'
    ];

    public static $searchable = [
        'name',
        'guard_name'
    ];
}
