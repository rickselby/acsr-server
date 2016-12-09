<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property $id
 * @property $name
 * @property $new
 * @property $on_server
 * @property $timezone
 */
class User extends Authenticatable
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'new' => 'boolean',
        'on_server' => 'boolean',
    ];

    /**
     * The providers this user can log in with
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providers()
    {
        return $this->hasMany(UserProvider::class);
    }

    /**
     * Get a specific provider for this user
     * @param $provider
     * @return mixed
     */
    public function getProvider($provider)
    {
        return $this->providers->where('provider', $provider)->first();
    }

    public function adminEvents()
    {
        return $this->belongsToMany(Event::class, 'event_admins');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_signups');
    }

}
