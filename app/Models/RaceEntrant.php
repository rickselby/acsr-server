<?php

namespace App\Models;

/**
 * @property $id
 * @property $event_id
 * @property $user_id
 */
class RaceEntrant extends \Eloquent
{
    protected $casts = [
        'grid' => 'integer',
        'position' => 'integer',
        'time' => 'integer',
        'laps' => 'integer',
        'fastest_lap' => 'integer',
    ];

    protected function race()
    {
        return $this->hasOne(Race::class);
    }

    protected function user()
    {
        return $this->hasOne(User::class);
    }
}
