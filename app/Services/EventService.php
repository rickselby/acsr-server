<?php

namespace App\Services;

use App\Contracts\VoiceServerContract;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\PointsSequence;
use App\Services\Events\PreparationService;
use Carbon\Carbon;

class EventService
{
    /** @var PreparationService */
    protected $preparationService;

    public function __construct(PreparationService $preparationService)
    {
        $this->preparationService = $preparationService;
    }

    /**
     * Create an event from the request
     *
     * @param EventRequest $request
     *
     * @return Event
     */
    public function create(EventRequest $request)
    {
        $event = new Event();
        $this->update($event, $request);
        $event->save();
        $event->admins()->attach(\Auth::user());
        return $event;
    }

    /**
     * Update an event from a request
     *
     * @param Event $event
     * @param EventRequest $request
     */
    public function update(Event $event, EventRequest $request)
    {
        $event->fill($request->all());
        $event->pointsSequence()->associate(
            PointsSequence::find($request->get('points_sequence_id'))
        );
        $event->save();
    }

    /**
     * Update the server config for the given event
     *
     * @param Event $event
     * @param $config
     */
    public function updateConfig(Event $event, $config)
    {
        $event->config = $config;
        $event->save();
    }

    /**
     * Get a list of upcoming events
     * If a user is signed in, add their signup status
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function openEvents()
    {
        $events = Event::whereNull('started')->orderBy('start')->get();

        foreach ($events AS $event) {
            // If there is a user logged in, add whether they are signed up or not
            if (\Auth::check()) {
                $event->signed_up = $event->signups->contains(\Auth::user());
            }
            $maxSlots = $this->preparationService->getMaxSlots($event);
            if ($maxSlots != PHP_INT_MAX) {
                $event->max_slots = $maxSlots;
            }
        }

        return $events;
    }

    /**
     * Get a list of completed events
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function pastEvents()
    {
        return Event::whereNotNull('started')->orderBy('start', 'desc')->get();
    }

    /**
     * Sign up the current user to the given event
     *
     * @param Event $event
     */
    public function signup(Event $event)
    {
        if (!$event->signups->contains(\Auth::user())) {

            // Check there is still room, given the number of servers
            if ($this->preparationService->getMaxSlots($event) > $event->signups->count()) {
                $event->signups()->attach(\Auth::user());
            } else {
                \Notification::add('warning', 'Sorry, that event is full');
            }

        }
    }

    /**
     * Remove the current user from the signups for the given event
     *
     * @param Event $event
     */
    public function cancelSignup(Event $event)
    {
        if ($event->signups->contains(\Auth::user())) {
            $event->signups()->detach(\Auth::user());
        }
    }

    /**
     * Try to create servers for upcoming events
     */
    public function createServers()
    {
        foreach($this->openEvents() AS $event) {
            // Check if we're within 45 minutes of the start of the event
            if ($event->start->subMinutes(45)->lt(Carbon::now())) {
                // Check the servers haven't already been created
                if (!$event->servers->count()) {
                    // Create the servers for this event
                    $this->preparationService->createServers($event);
                }
            }
        }
    }

}
