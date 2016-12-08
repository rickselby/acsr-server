<?php

namespace App\Facades\Socialite;

use App\Services\Socialite\Providers;
use \Illuminate\Support\Facades\Facade;

class ProvidersFacade extends Facade {
    protected static function getFacadeAccessor() { return Providers::class; }
}