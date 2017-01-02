<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\Events\StandingsService;
use App\Services\EventService;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Show the list of events
     *
     * @param EventService $eventService
     *
     * @return \Illuminate\View\View
     */
    public function index(EventService $eventService)
    {
        return view('event.index')
            ->with('open', $eventService->openEvents())
            ->with('past', $eventService->pastEvents());
    }

    /**
     * Show results for an event
     *
     * @param Event $event
     * @param StandingsService $standingsService
     *
     * @return \Illuminate\View\View
     */
    public function show(Event $event, StandingsService $standingsService)
    {
        return view('event.show')
            ->with('event', $event)
            ->with('heatStandings', $standingsService->heatStandings($event));
    }

    /**
     * Sign the current user up to an event
     *
     * @param Event $event
     * @param EventService $eventService
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signup(Event $event, EventService $eventService)
    {
        $eventService->signup($event);
        return \Redirect::route('event.index');
    }

    /**
     * Cancel a signup for the current user
     *
     * @param Event $event
     * @param EventService $eventService
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSignup(Event $event, EventService $eventService)
    {
        $eventService->cancelSignup($event);
        return \Redirect::route('event.index');
    }

}