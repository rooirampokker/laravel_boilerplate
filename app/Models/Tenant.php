<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'tenancy_db_name',
    ];

    /**
     * Add Custom columns (that won't be stored in the data JSON column) here.
     * Id column should always be included, even though it's not custom
     * @return string[]
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'description',
            'tenancy_db_name',
            'deleted_at'
        ];
    }
}
