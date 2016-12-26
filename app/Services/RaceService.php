<?php

namespace App\Services;

use App\Contracts\ConfigFileContract;
use App\Contracts\ServerManagerContract;
use App\Contracts\VoiceServerContract;
use App\Models\Race;
use App\Models\Server;
use App\Services\Events\DashboardService;

class RaceService
{
    /** @var ServerManagerContract */
    protected $serverManager;
    /** @var ConfigFileContract */
    protected $configService;
    /** @var VoiceServerContract */
    protected $voiceService;

    public function __construct(
        ServerManagerContract $serverManagerContract,
        ConfigFileContract $configFileContract,
        VoiceServerContract $voiceServerContract
    )
    {
        $this->serverManager = $serverManagerContract;
        $this->configService = $configFileContract;
        $this->voiceService = $voiceServerContract;
    }

    /**
     * Set results for the race running on the server identified by the given IP
     *
     * @param $ip
     * @param $results
     *
     * @throws \Exception
     */
    public function setResults($ip, $results)
    {
        $this->voiceService->postLog('Got results from '.$ip);
        // look up the server by ip
        $server = Server::where('ip', $ip)->first();

        if (!$server) {
            $this->voiceService->postLog('Could not find server by '.$ip);
            throw new \Exception('Could not find server by IP');
        }

        // get the race that's running on that server
        $race = Race::find($server->race_id);

        if (!$race) {
            $this->voiceService->postLog('Could not find race on server '.$server->id.' ('.$ip.')');
            throw new \Exception('Could not find race by server IP');
        }

        // apply the results to that race
        $this->saveResults($results, $race);

        // mark the race as complete, do whatever is needed
        $this->completeRace($race);

        return $race;
    }


    /**
     * Start a race on a random server
     *
     * @param Race $race
     */
    public function startRace(Race $race, $laps)
    {
        // Get a random server
        $server = $race->event->servers()->whereNull('race_id')->inRandomOrder()->first();

        $this->voiceService->postLog('Starting race "'.$race->name.'" on server '.$server->id.' ('.$server->ip.')');

        // Set that this race is happening on this server
        $server->race_id = $race->id;
        $server->save();

        // Get the config file
        $config = $this->configService->alterServerConfig($race->event->config, [
            'SERVER.NAME' => $race->event->name.': '.$race->name,
            'RACE.LAPS' => $laps
        ]);

        // Get the entry list
        $entryList = $this->configService->getEntryList(
            $race->event->car_model,
            $race->entrants()->grid()->get()->all()
        );

        // Pass the settings to the server
        $this->serverManager->setConfig($server, $config);
        $this->serverManager->setEntryList($server, $entryList);

        // And start the server!
        $this->serverManager->start($server);
        $this->voiceService->postAnnoucement('<@&'.$race->group_id.'> server is up!');
        $race->active = true;
        $race->save();
    }


    /**
     * Set up a voice group for a race
     *
     * @param Race $race
     */
    public function setupVoiceGroup(Race $race)
    {
        $this->voiceService->postLog('Creating Group "'.$race->name.'"');

        // Get users
        $users = [];
        foreach($race->entrants AS $entrant) {
            $users[] = $entrant->user;
        }

        // Create a group for these users
        $group = $this->voiceService->createGroup(
            $race->name,
            $users
        );

        // Create a voice channel for this group
        $channel = $this->voiceService->createVoiceChannel($race->name, [$group]);

        // Save the group ID so it can be referenced later
        $race->group_id = $group;
        $race->channel_id = $channel;
        $race->save();
    }

    /**
     * Parse results JSON and save to results entrants
     *
     * @param string $resultsJson
     * @param Race $race
     */
    protected function saveResults($resultsJson, Race $race)
    {
        $this->voiceService->postLog('Saving results for "'.$race->name.'"');
        // Save the results JSON file
        \File::put($this->getResultsPath($race), $resultsJson);

        // get a list of entrants keyed by steam ID
        $entrants = [];
        foreach($race->entrants AS $entrant) {
            $entrants[$entrant->user->getProvider('steam')->provider_user_id] = $entrant;
        }

        // Decode the json ready for reading
        $results = json_decode($resultsJson);

        $position = 1;
        foreach($results->Result AS $result) {
            if (isset($entrants[$result->DriverGuid])) {
                $entrants[$result->DriverGuid]->position = $position++;
                $entrants[$result->DriverGuid]->time = $result->TotalTime;
                $entrants[$result->DriverGuid]->fastest_lap = $result->BestLap;
                $entrants[$result->DriverGuid]->laps = 0;
                $entrants[$result->DriverGuid]->save();
            }
        }

        // Count how many laps each entrant did
        foreach($results->Laps AS $lap) {
            if (isset($entrants[$lap->DriverGuid])) {
                $entrants[$lap->DriverGuid]->laps++;
                $entrants[$lap->DriverGuid]->save();
            }
        }
    }

    /**
     * Complete the race
     *
     * @param Race $race
     */
    protected function completeRace(Race $race)
    {
        $this->voiceService->postLog('Marking race "'.$race->name.'" complete');

        // Mark the race as complete
        $race->active = false;
        $race->complete = true;
        $race->save();

        // Stop the server and clear the race
        $server = Server::where('race_id', $race->id)->first();
        $this->serverManager->stop($server);
        $server->race_id = null;
        $server->save();

        // Delete stuff from the voice server
        $this->voiceService->destroyVoiceChannel($race->channel_id);
        $this->voiceService->destroyGroup($race->group_id);
    }

    public function getResultsPath(Race $race)
    {
        return storage_path('app/results/'.$race->id.'.json');
    }
}
