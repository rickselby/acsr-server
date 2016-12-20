<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Point;
use App\Models\PointsSequence;

class PointsSequenceService
{
    /**
     * Set the points for the given sequence
     *
     * @param  PointsSequence $sequence
     * @param  int[] $pointsList
     *
     * @throws \Exception
     */
    public function setPoints(PointsSequence $sequence, $pointsList)
    {
        foreach($pointsList AS $position => $points) {
            // Get the current points, or create...
            /** @var Point $point */
            $point = $sequence->points->where('position', $position)->first();
            if (!$point) {
                // Create a new point, if it's not zero
                if ($points != 0) {
                    $point = new Point(['position' => $position, 'points' => $points]);
                    $sequence->points()->save($point);
                }
            } else {
                // Update points, or delete if zero
                if ($points != 0) {
                    $point->points = $points;
                    $point->save();
                } else {
                    $point->delete();
                }
            }
        }
    }

    /**
     * Check if a given sequence is in use somewhere.
     *
     * @param  PointsSequence $sequence
     *
     * @return bool
     */
    public function isUsed(PointsSequence $sequence)
    {
        // Build an array of sequence ids that are in use
        $usedSequences = [];
        foreach(Event::all() AS $event) {
            $usedSequences[] = $event->pointsSequence->id;
        }

        // Check the array
        return in_array($sequence->id, $usedSequences);
    }

    /**
     * Get a list of sequences for a drop-down select
     *
     * @return \Illuminate\Support\Collection
     */
    public function forSelect()
    {
        return PointsSequence::orderBy('name')->pluck('name', 'id');
    }

    /**
     * Get a sequence as a keyed array [position => points]
     *
     * @param PointsSequence $sequence
     *
     * @return array
     */
    public function get(PointsSequence $sequence)
    {
        $positions = [];
        foreach($sequence->points AS $point) {
            $positions[$point->position] = $point->points;
        }
        return $positions;
    }
}
