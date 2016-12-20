<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\GridsContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Services\Events\PreparationService;
use App\Services\EventService;
use App\Services\PointsSequenceService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $resourceAbilityMap = [
        'show' => 'manage',
        'edit' => 'manage',
        'update' => 'manage',
        'destroy' => 'manage',
        'verifyDestroy' => 'manage',
        'config' => 'manage',
    ];

    public function __construct()
    {
        $this->middleware('can:event-admin');
        $this->authorizeResource(Event::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.event.index')
            ->with('events', Event::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  GridsContract $gridsService
     * @param  PointsSequenceService $pointsSequenceService
     *
     * @return \Illuminate\Http\Response
     */
    public function create(GridsContract $gridsService, PointsSequenceService $pointsSequenceService)
    {
        return view('admin.event.create')
            ->with('validation', $gridsService->validDescription())
            ->with('pointsSequenceSelect', $pointsSequenceService->forSelect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventRequest $request
     * @param EventService $eventService
     *
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request, EventService $eventService)
    {
        $event = $eventService->create($request);
        return \Redirect::route('admin.event.show', $event);
    }

    /**
     * Display the specified resource.
     *
     * @param  Event $event
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event, PreparationService $preparationService)
    {
        return view('admin.event.show')
            ->with('event', $event)
            ->with('valid', $preparationService->isValid($event));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Event $event
     * @param  GridsContract $gridsService
     * @param  PointsSequenceService $pointsSequenceService
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event, GridsContract $gridsService, PointsSequenceService $pointsSequenceService)
    {
        return view('admin.event.edit')
            ->with('event', $event)
            ->with('validation', $gridsService->validDescription())
            ->with('pointsSequenceSelect', $pointsSequenceService->forSelect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EventRequest  $request
     * @param  Event $event
     * @param  EventService $eventService
     *
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, Event $event, EventService $eventService)
    {
        $eventService->update($event, $request);
        \Notification::add('success', 'Event updated');
        return \Redirect::route('admin.event.show', $event);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Event $event
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        $event->delete();
        \Notification::add('success', 'Event deleted');
        return \Redirect::route('admin.event.index');
    }

    /**
     * Verify that we really want to delete this event
     *
     * @param Event $event
     */
    public function verifyDestroy(Event $event)
    {
        return view('admin.event.destroy')
            ->with('event', $event);
    }

    /**
     * Update the server config for an event
     *
     * @param Request $request
     * @param Event $event
     * @param EventService $eventService
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function config(Request $request, Event $event, EventService $eventService)
    {
        $eventService->updateConfig($event, $request->get('config'));
        \Notification::add('success', 'Server Config updated');
        return \Redirect::route('admin.event.show', $event);
    }

}
