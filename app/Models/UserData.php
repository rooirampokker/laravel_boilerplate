<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $respondent_id
 * @property string $key
 * @property string $value
 * @property string $created_at
 * @property string $updated_at
 * @property Respondent $respondent
 */
class UserData extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_data';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'key', 'value', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
