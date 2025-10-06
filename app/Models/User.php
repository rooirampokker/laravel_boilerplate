<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

use Askedio\SoftCascade\Traits\SoftCascadeTrait;
use App\Traits\SearchableTrait;
use App\Traits\FilterableTrait;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use softDeletes;
    use HasUlids;
    use SoftCascadeTrait;
    use SearchableTrait;
    use FilterableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'password'
    ];

    public static $searchable = [
        'email',
        'first_name',
        'last_name'
    ];
    protected $softCascade = [
        'data'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function data()
    {
        return $this->hasMany('App\Models\UserData');
    }

    //ensures the password is always encrypted
    protected function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }
}
