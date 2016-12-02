<?php

namespace App\Services\Grids\LeftRightGrids;

class AssignRaces
{
    /**
     * This is totally random. It'd be nice to pseudo-randomise this; try to avoid
     * drivers starting alongside the same driver, and try to race against as wide a
     * spread of drivers as possible.
     *
     * @param $sessions
     *
     * @return array
     */
    public function assign($sessions)
    {
        $races = [];

        foreach($sessions AS $sessionID => $session) {
            for ($i = 1; $i <= $session['heats']; $i++) {
                $race = [
                    'slots' => [],
                    'session' => $sessionID,
                ];
                foreach($session['slots'] AS $slotID => $drivers) {
                    $race['slots'][$slotID] = [];

                    // randomly pick 2 drivers from this session slot for this race slot
                    for ($d = 0; $d < 2; $d++) {
                        $id = array_rand($sessions[$sessionID]['slots'][$slotID]);
                        $race['slots'][$slotID][] = $sessions[$sessionID]['slots'][$slotID][$id];
                        unset($sessions[$sessionID]['slots'][$slotID][$id]);
                    }
                }
                $races[] = $race;
            }
        }

        return $races;
    }
}
