<?php

namespace App\Models;

/**
 * @property $id
 * @property $provider_id
 * @property $event_id
 * @property $ip
 * @property $password
 * @property $settings
 */
class Server extends \Eloquent
{
    protected $fillable = ['provider_id', 'ip', 'password', 'settings'];

    protected $casts = [
        'event_id' => 'integer',
        'provider_id' => 'integer',
        'settings' => 'array',
    ];

    /**
     * Get all servers for a given event
     *
     * @param $query
     * @param $eventID
     *
     * @return mixed
     */
    public function scopeForEvent($query, $eventID)
    {
        return $query->where('event_id', $eventID);
    }
}
