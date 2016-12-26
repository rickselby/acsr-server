<?php

namespace App\Services\Events;

use App\Contracts\ServerManagerContract;
use App\Contracts\ServerProviderContract;
use App\Contracts\VoiceServerContract;
use App\Models\Event;
use App\Models\Race;
use App\Services\RaceService;
use Carbon\Carbon;

class DashboardService
{
    /** @var VoiceServerContract */
    protected $voiceService;
    /** @var $raceService */
    protected $raceService;
    /** @var ServerProviderContract */
    protected $serverProviderService;
    /** @var ServerManagerContract */
    protected $serverManagerService;

    public function __construct(
        VoiceServerContract $voiceServerContract,
        RaceService $raceService,
        FinalsService $finalsService,
        ServerProviderContract $serverProviderContract,
        ServerManagerContract $serverManagerContract
    )
    {
        $this->voiceService = $voiceServerContract;
        $this->raceService = $raceService;
        $this->finalsService = $finalsService;
        $this->serverProviderService = $serverProviderContract;
        $this->serverManagerService = $serverManagerContract;
    }

    /**
     * Get a list of sections to show
     *
     * @param Event $event
     *
     * @return array
     */
    public function sections(Event $event)
    {
        return [
            /**
             * These could be grouped together, but this allows a little more flexibility
             * should anything need to be changed.
             */
            'signups' => !$event->started,
            'grids' => !$event->started,
            // Can't start until we've generated the grids!
            'start-heats' => $event->races->count() && !$event->started,
            'races' => $event->races->count(),
            // Show heat standings once we're up and running
            'heat-standings' => $event->started,
            'start-finals' => $this->canStartFinals($event),
        ];
    }

    /**
     * Progress an event after the given race has completed
     *
     * @param Race $race
     */
    public function progress(Race $race)
    {
        if ($race->heat) {
            // anything heat-specific to do? don't think so...
        } else {
            $this->finalsService->progress($race);
            $this->checkForComplete($race->event);
        }

        $this->startNextSession($race->event);
    }

    /**
     * Check if we can start the finals for the given event
     *
     * @param Event $event
     *
     * @return bool
     */
    public function canStartFinals(Event $event)
    {
        // Check that there have been any number of heats
        $heats = $event->races->where('heat', true);
        if (!count($heats)) {
            return false;
        }
        // Check that all heats are complete
        $complete = $heats->where('complete', true);
        if ($heats->count() !== $complete->count()) {
            return false;
        }
        // Check that there are no other races
        return ($event->races->where('heat', false)->count() == 0);
    }

    /**
     * Get the list of servers for the event and their status
     *
     * @param Event $event
     *
     * @return array
     */
    public function serverStatus(Event $event)
    {
        $servers = [];
        foreach($event->servers AS $server) {
            $servers[] = [
                'server' => $server,
                'available' => $this->serverManagerService->isAvailable($server),
            ];
        }
        return $servers;
    }

    /**
     * Run the next session for the given event
     *
     * @param Event $event
     */
    public function startNextSession(Event $event)
    {
        $session = $this->getNextSession($event);

        // Check we should be starting this session
        // The above could return a part-complete session
        if ($this->shouldSessionBeRun($event, $session)) {
            $this->voiceService->postLog('Starting races for session '.$session);

            $races = $event->races->where('session', $session);

            foreach ($races AS $race) {
                $this->raceService->startRace($race, $event->laps_per_heat);
            }
        }
    }

    /**
     * Get the next session to run
     *
     * @param Event $event
     *
     * @return integer
     */
    protected function getNextSession(Event $event)
    {
        return \DB::table('races')
            ->where('event_id', $event->id)
            ->where('complete', false)
            ->orderBy('session')
            ->limit(1)
            ->value('session');
    }

    /**
     * Check if all races in the given session are complete
     *
     * @param Event $event
     * @param $session
     *
     * @return bool
     */
    protected function shouldSessionBeRun(Event $event, $session)
    {
        $races = $event->races->where('session', $session);

        // Races must exist to run a session!
        if (!$races->count()) {
            return false;
        }

        // If any race in this session is active or complete, it's already running
        // (but incomplete)
        foreach($races AS $race) {
            if ($race->active || $race->complete) {
                return false;
            }
        }

        // There are races, and none of them are active or complete, so we can run it
        return true;
    }

    /**
     * See if the event is complete, and shut down the servers when it is
     *
     * @param Event $event
     */
    protected function checkForComplete(Event $event)
    {
        // Check we have both heats and finals, and ALL are complete
        if ($event->races->where('heat', true)->count()
            &&
            $event->races->where('heat', false)->count()
            &&
            (
                $event->races->count() == $event->races->where('complete', true)->count()
            )
        ) {
            $event->finished = Carbon::now();
            $event->save();

            $this->voiceService->postLog('Shutting down servers!');
            // Start shutting things down!
            foreach($event->servers AS $server) {
                $this->serverProviderService->destroy($server);
            }
        }
    }

}
