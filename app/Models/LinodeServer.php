<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinodeServer extends Model
{
    protected $casts = [
        'event_id' => 'integer',
        'linode_id' => 'integer',
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
