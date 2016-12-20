<?php

namespace App\Services\Events;

use App\Models\Event;
use App\Services\EventService;
use App\Services\RaceService;
use App\Services\UserService;
use Carbon\Carbon;

class HeatsService
{
    /** @var UserService */
    protected $userService;
    /** @var RaceService */
    protected $raceService;
    /** @var EventService */
    protected $eventService;

    public function __construct(
        UserService $userService,
        RaceService $raceService
    )
    {
        $this->userService = $userService;
        $this->raceService = $raceService;
    }

    /**
     * Start running an event
     *
     * @param Event $event
     */
    public function initialise(Event $event)
    {
        // wait! set up the groups and stuff first!
        $this->setupVoiceGroups($event);

        // Set the event as started!
        $event->started = Carbon::now();
        $event->save();
    }

    /**
     * Set up all voice groups for an event
     *
     * @param Event $event
     */
    protected function setupVoiceGroups(Event $event)
    {
        // First, check again who is on the server...
        $this->userService->updateNames();

        foreach($event->races->where('heat', true) AS $race) {
            $this->raceService->setupVoiceGroup($race);
        }
    }

}
