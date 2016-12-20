<?php

namespace App\Models;


/**
 * @property $id
 * @property $position
 * @property $points
 */
class Point extends \Eloquent
{
    protected $fillable = ['position', 'points'];

    protected $casts = [
        'position' => 'integer',
        'points' => 'integer',
    ];

    public function sequence()
    {
        return $this->belongsTo(PointsSequence::class);
    }

}
