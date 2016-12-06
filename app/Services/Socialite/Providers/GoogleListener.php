<?php

namespace App\Services\Socialite\Providers;

use SocialiteProviders\Manager\SocialiteWasCalled;

class GoogleListener
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('google', Google::class);
    }
}
