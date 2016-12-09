<?php

namespace App\Services;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventService
{
    /**
     * Create an event from the request
     *
     * @param EventRequest $request
     *
     * @return Event
     */
    public function create(EventRequest $request)
    {
        $event = Event::create($request->all());
        $event->admins()->attach(\Auth::user());
        $event->save();
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
        $events = Event::where('start', '>', Carbon::now())->get();

        // If there is a user logged in, add whether they are signed up or not
        if (\Auth::check()) {
            foreach ($events AS $event) {
                $event->signedup = $event->signups->contains(\Auth::user());
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
        return Event::where('start', '<', Carbon::now())->get();
    }

    /**
     * Sign up the current user to the given event
     *
     * @param Event $event
     */
    public function signup(Event $event)
    {
        if (!$event->signups->contains(\Auth::user())) {
            $event->signups()->attach(\Auth::user());
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

}
