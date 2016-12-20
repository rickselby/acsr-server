<?php

namespace App\Models;

/**
 * @property $id
 * @property $event_id
 * @property $user_id
 * @property $grid
 * @property $position
 * @property $time
 * @property $laps
 * @property $fastest_lap
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

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeGrid($query)
    {
        return $query->orderBy('grid');
    }

    public function scopeResults($query)
    {
        return $query->orderBy('position');
    }
}
