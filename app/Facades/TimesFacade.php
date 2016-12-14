<?php

namespace App\Facades;

use App\Services\Times;
use \Illuminate\Support\Facades\Facade;

class TimesFacade extends Facade {
    protected static function getFacadeAccessor() { return Times::class; }
}
