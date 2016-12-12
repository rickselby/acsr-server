<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
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
    protected function scopeForEvent($query, $eventID)
    {
        return $query->where('event_id', $eventID);
    }
}
