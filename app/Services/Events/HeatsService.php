<?php

namespace App\Services\Events;

use App\Models\Event;
use Carbon\Carbon;

class HeatsService
{
    /**
     * Start running an event
     *
     * @param Event $event
     */
    public function initialise(Event $event)
    {
        // Set the event as started!
        $event->started = Carbon::now();
        $event->save();
    }


}
