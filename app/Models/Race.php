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
 */
class Race extends \Eloquent
{
    protected $casts = [
        'heat' => 'boolean',
        'session' => 'integer',
        'active' => 'boolean',
    ];

    public function event()
    {
        return $this->hasOne(Event::class);
    }

}
