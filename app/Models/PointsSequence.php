<?php

namespace App\Models;

/**
 * @property $id
 * @property $name
 */
class PointsSequence extends \Eloquent
{
    protected $fillable = ['name'];

    public function points()
    {
        return $this->hasMany(Point::class)->orderBy('position');
    }
}
