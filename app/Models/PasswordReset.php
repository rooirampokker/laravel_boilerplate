<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class PasswordReset extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'email', 'token'
    ];
}
