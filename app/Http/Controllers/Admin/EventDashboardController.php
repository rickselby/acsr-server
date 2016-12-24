<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Events\FinalsService;
use App\Services\Events\HeatsService;
use App\Services\Events\PreparationService;
use App\Services\Events\StandingsService;
use \Illuminate\Http\Request;
use App\Models\Event;
use App\Services\Events\DashboardService;

class EventDashboardController extends Controller
{
    /** @var DashboardService */
    protected $dashboardService;
    /** @var PreparationService */
    protected $preparationService;
    /** @var HeatsService */
    protected $heatsService;
    /** @var StandingsService */
    protected $standingsService;
    /** @var FinalsService */
    protected $finalsService;

    public function __construct(
        DashboardService $dashboardService,
        PreparationService $preparationService,
        HeatsService $heatsService,
        StandingsService $standingsService,
        FinalsService $finalsService
    )
    {
        $this->dashboardService = $dashboardService;
        $this->preparationService = $preparationService;
        $this->heatsService = $heatsService;
        $this->standingsService = $standingsService;
        $this->finalsService = $finalsService;

        $this->middleware('can:manage,event');
    }

    public function dashboard(Event $event)
    {
        return view('admin.event.dashboard')
            ->with('event', $event)
            ->with('servers', $this->dashboardService->serverStatus($event))
            ->with('sections', $this->dashboardService->sections($event))
            ->with('maxDrivers', $this->preparationService->getMaxSlots($event))
            ->with('heatStandings', $this->standingsService->heatStandings($event))
            ;
    }

    public function grids(Event $event)
    {
        $this->preparationService->generateGrids($event);
        \Notification::add('success', 'Grids Generated');
        return \Redirect::route('admin.event.dashboard', $event);
    }

    public function destroySignup(Event $event, User $user)
    {
        $event->signups()->detach($user);
        return \Redirect::route('admin.event.dashboard', $event);
    }

    public function startHeats(Event $event)
    {
        $this->heatsService->initialise($event);
        $this->dashboardService->startNextSession($event);
        return \Redirect::route('admin.event.dashboard', $event);
    }

    public function startFinals(Event $event)
    {
        $this->finalsService->initialise($event);
        $this->dashboardService->startNextSession($event);
        return \Redirect::route('admin.event.dashboard', $event);
    }

}
