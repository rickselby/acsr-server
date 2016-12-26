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
 * @property $number
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
        'name', 'number',
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

    /**
     * Link the skin to the driver numbe
     *
     * @return mixed
     */
    public function getSkinAttribute()
    {
        return $this->number;
    }

    /**
     * Check if the given user has the required providers
     *
     * @return bool
     */
    public function hasRequiredProviders()
    {
        foreach(\AuthProviders::required() AS $provider) {
            if (!$this->getProvider($provider)) {
                return false;
            }
        }

        // All required providers found
        return true;
    }
}
