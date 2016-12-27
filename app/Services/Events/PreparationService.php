<?php

namespace App\Services\Events;

use App\Contracts\GridsContract;
use App\Contracts\ServerProviderContract;
use App\Contracts\VoiceServerContract;
use App\Models\Event;
use App\Models\Race;
use App\Models\RaceEntrant;
use App\Models\User;

class PreparationService
{
    /** @var ServerProviderContract */
    protected $serverProviderService;
    /** @var GridsContract */
    protected $gridsService;
    /** @var VoiceServerContract  */
    protected $voiceService;

    public function __construct(ServerProviderContract $serverProviderService, GridsContract $gridsService, VoiceServerContract $voiceServerContract)
    {
        $this->serverProviderService = $serverProviderService;
        $this->gridsService = $gridsService;
        $this->voiceService = $voiceServerContract;
    }

    /**
     * Check if the given event is valid
     *
     * @param Event $event
     *
     * @return bool
     */
    public function isValid(Event $event)
    {
        $this->setupGridsService($event);
        return $this->gridsService->isValid();
    }

    /**
     * Get the maximum slots available for an event
     *
     * @param Event $event
     *
     * @return int
     */
    public function getMaxSlots(Event $event)
    {
        $this->setupGridsService($event);
        if ($event->servers->count()) {
            return $this->gridsService->maxDriversFor($event->servers->count());
        } else {
            // this is overkill, but basically, signup is open...
            return PHP_INT_MAX;
        }
    }

    /**
     * Create the servers
     *
     * @param Event $event
     */
    public function createServers(Event $event)
    {
        $this->setupGridsService($event);
        // Get the signup
        if ($this->gridsService->isValid()) {

            $signupCount = $event->signups->count();

            $serverCount = $this->gridsService->serversNeeded($signupCount);

            for ($i = 0; $i < $serverCount; $i++) {
                $this->serverProviderService->create($event);
            }
        }
    }

    /**
     * Generate the grids for the heats
     * @param Event $event
     */
    public function generateGrids(Event $event)
    {
        $this->clearGrids($event);
        $this->setupGridsService($event);
        $grids = $this->gridsService->generateGrids(
            $this->getValidSignups($event)
        );

        foreach($grids AS $k => $grid) {
            $race = new Race();
            $race->fill([
                'heat' => true,
                'name' => 'Heat '.($k+1),
                'session' => $grid['session'],
            ]);
            $event->races()->save($race);

            // Show the grid in order
            ksort($grid['grid']);

            // Prepare the announcement
            $announcement = $race->name.': ';

            foreach($grid['grid'] AS $gridSlot => $user) {
                $entrant = new RaceEntrant();
                $entrant->grid = $gridSlot;
                $entrant->user()->associate($user);
                $race->entrants()->save($entrant);

                // Populate the announcement
                $announcement .= ' **'.$gridSlot.'.** ';
                // Everyone should have discord, but...
                if ($user->getProvider('discord'))
                {
                    $announcement .= '<@'.$user->getProvider('discord')->provider_user_id.'> ';
                } else {
                    $announcement .= $user->name.' ';
                }
            }

            // Send the announcement about the grid
            $this->voiceService->postAnnoucement($announcement);
        }
    }

    /**
     * Set up the grids class
     *
     * @param Event $event
     */
    protected function setupGridsService(Event $event)
    {
        $this->gridsService->setOptions(
            $event->drivers_per_heat,
            $event->heats_per_driver
        );
    }

    /**
     * Clear any generated grids
     *
     * @param Event $event
     */
    protected function clearGrids(Event $event)
    {
        foreach($event->races AS $race) {
            $race->delete();
        }
    }

    /**
     * Get a list of valid signups
     *
     * @param Event $event
     *
     * @return User[]
     */
    protected function getValidSignups(Event $event)
    {
        $signups = [];
        foreach($event->signups AS $user) {
            if ($user->hasRequiredProviders()) {
                $signups[] = $user;
            }
        }
        return $signups;
    }
}
