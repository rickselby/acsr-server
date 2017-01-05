<?php

namespace App\Services\Events;

use App\Contracts\VoiceServerContract;
use App\Models\Event;
use App\Models\Race;
use App\Models\RaceEntrant;
use App\Services\EventService;
use App\Services\RaceService;
use App\Services\UserService;

class FinalsService
{
    /** @var StandingsService */
    protected $standingsService;
    /** @var UserService */
    protected $userService;
    /** @var RaceService */
    protected $raceService;
    /** @var EventService */
    protected $eventService;
    /** @var VoiceServerContract */
    protected $voiceService;

    public function __construct(
        StandingsService $standingsService,
        UserService $userService,
        RaceService $raceService,
        EventService $eventService,
        VoiceServerContract $voiceServerContract
    )
    {
        $this->standingsService = $standingsService;
        $this->userService = $userService;
        $this->raceService = $raceService;
        $this->eventService = $eventService;
        $this->voiceService = $voiceServerContract;
    }

    /**
     * Start the finals
     *
     * @param Event $event
     */
    public function initialise(Event $event)
    {
        $this->createFinals($event);
    }

    /**
     * Progress from one final to the next
     *
     * @param Race $race
     */
    public function progress(Race $race)
    {
        // Is there a next race?
        $nextRace = $race->event->races->where('session', $race->session + 1)->first();

        if ($nextRace) {
            // Get results of the given race
            $results = $race->entrants()->results()->get();

            // Get the next grid slot of the race being added to
            $nextGrid = $nextRace->entrants->count() + 1;

            $users = [];
            for ($i = 0; $i < $race->event->advance_per_final; $i++) {
                // Create a new entrant for the next race
                $entrant = new RaceEntrant();
                $entrant->grid = $nextGrid;
                $entrant->user()->associate($results[$i]->user);
                $nextRace->entrants()->save($entrant);

                // Make a list of the users
                $users[] = $results[$i]->user;

                // Increment the grid slot
                $nextGrid++;
            }

            // Reload the entrants, just in case
            $nextRace->load(['entrants']);

            // Re-post the grid for the final, with the new entrants
            $this->raceService->postGrid($nextRace);

            // Add these users to the voice group for the next race
            $this->voiceService->addToGroup($nextRace->group_id, $users);
        }

    }

    /**
     * Create the finals!
     *
     * @param Event $event
     */
    protected function createFinals(Event $event)
    {
        // Get the post-heat standings
        $standings = $this->standingsService->heatStandings($event);

        // Filter out just the users
        array_walk($standings, function(&$item) {
            $item = $item['user'];
        });

        // Break the standings array into race-sized chunks
        $races = array_chunk($standings, $event->drivers_per_final);

        end($races);
        $lastRaceKey = key($races);

        // Don't run the last race if it's too small to be worth it
        if (count($races[$lastRaceKey]) < ($event->advance_per_final * 2)) {
            $newLastRace = $lastRaceKey - 1;
            $races[$newLastRace] = array_merge($races[$newLastRace], $races[$lastRaceKey]);
            unset($races[$lastRaceKey]);
        }

        // Assign letters to the finals
        $alphas = range('A', 'Z');
        foreach($races AS $id => $entrants) {
            $races[$id] = [
                'letter' => $alphas[$id],
                'entrants' => $entrants,
            ];
        }

        // Work through the races in reverse order
        foreach(array_reverse($races) AS $race) {
            $this->addRace($event, $race['letter'], $race['entrants']);
        }
    }

    /**
     * Add another race to the event
     *
     * @param Event $event
     * @param $letter
     * @param $entrants
     */
    protected function addRace(Event $event, $letter, $entrants)
    {
        // get next session number
        $nextSession = $event->races->max('session') + 1;

        // create the race
        $race = new Race();
        $race->fill([
            'heat' => false,
            'name' => $letter.' Final',
            'session' => $nextSession,
        ]);
        $event->races()->save($race);
        $event->load(['races']);

        // add the entrants, in order
        foreach($entrants AS $key => $user) {
            $entrant = new RaceEntrant();
            $entrant->grid = $key + 1;
            $entrant->user()->associate($user);
            $race->entrants()->save($entrant);
        }

        $race->load(['entrants']);

        $this->raceService->setupVoiceGroup($race);
        $this->raceService->postGrid($race);
    }

}
