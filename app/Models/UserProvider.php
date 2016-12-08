<?php

namespace App\Models;

/**
 * @property $id
 * @property $user_id
 * @property $provider
 * @property $provider_user_id
 * @property $name
 * @property $avatar
 */
class UserProvider extends \Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider', 'provider_user_id', 'name', 'avatar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
