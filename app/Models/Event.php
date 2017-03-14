<?php

namespace App\Models;
use Collective\Html\Eloquent\FormAccessible;

/**
 * @property $id
 * @property $name
 * @property $start
 * @property $drivers_per_final
 * @property $advance_per_final
 * @property $laps_per_heat
 * @property $laps_per_final
 * @property $car_model
 * @property $automate
 * @property $config
 * @property $started
 * @property $finished
 */
class Event extends \Eloquent
{
    use FormAccessible;

    protected $dates = [
        'start',
        'started',
        'finished',
    ];

    protected $casts = [
        'drivers_per_heat' => 'integer',
        'heats_per_driver' => 'integer',
        'drivers_per_final' => 'integer',
        'advance_per_final' => 'integer',
        'laps_per_heat' => 'integer',
        'laps_per_final' => 'integer',
        'automate' => 'boolean',
        'config' => 'array',
    ];

    protected $fillable = [
        'name',
        'start',
        'drivers_per_final',
        'advance_per_final',
        'laps_per_heat',
        'laps_per_final',
        'car_model',
        'automate',
    ];

    public function races()
    {
        return $this->hasMany(Race::class)->orderBy('session')->orderBy('name');
    }

    public function signups()
    {
        return $this->belongsToMany(User::class, 'event_signups');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'event_admins');
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function pointsSequence()
    {
        return $this->belongsTo(PointsSequence::class);
    }

    /**
     * Check if the given user is an admin of this event
     *
     * @param User $user
     * @return mixed
     */
    public function isAdmin(User $user)
    {
        return $this->admins->contains($user);
    }

    public function formStartAttribute()
    {
        return $this->start ? $this->start->format('jS F Y, H:i') : '';
    }

}
