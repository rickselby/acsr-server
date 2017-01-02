<?php

namespace App\Models;

/**
 * @property $id
 * @property $event_id
 * @property $name
 * @property $heat
 * @property $session
 * @property $active
 * @property $server_id
 * @property $group_id
 * @property $channel_id
 */
class Race extends \Eloquent
{
    protected $fillable = ['heat', 'session', 'name'];

    protected $casts = [
        'heat' => 'boolean',
        'session' => 'integer',
        'active' => 'boolean',
        'complete' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function entrants()
    {
        return $this->hasMany(RaceEntrant::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'race_entrants');
    }

}
