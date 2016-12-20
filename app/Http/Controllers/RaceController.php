<?php

namespace App\Http\Controllers;

use App\Services\Events\DashboardService;
use App\Services\RaceService;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    /**
     * Take posted results and apply them to the relevant race
     *
     * @param Request $request
     * @param RaceService $raceService
     */
    public function results(Request $request, RaceService $raceService, DashboardService $eventDashboard)
    {
        if ($request->get('results')) {
            // Set the results for the race, get the race in question
            $race = $raceService->setResults($request->ip(), $request->get('results'));

            // check what needs to happen next to the surrounding event
            $eventDashboard->progress($race);
        }
    }
}
