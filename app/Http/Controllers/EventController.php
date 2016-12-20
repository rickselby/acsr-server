<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\Events\HeatsService;
use App\Services\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * Show the list of events
     *
     * @param EventService $eventService
     *
     * @return $this
     */
    public function index(EventService $eventService)
    {
        return view('event.index')
            ->with('open', $eventService->openEvents())
            ->with('past', $eventService->pastEvents());
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