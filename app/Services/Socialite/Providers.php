<?php

namespace App\Services\Socialite;

class Providers
{
    /**
     * Verify that the given provider is a valid provider
     *
     * @param string $provider
     *
     * @return bool
     */
    public function verify($provider)
    {
        return in_array($provider, $this->all());
    }

    /**
     * Get a list of all available providers
     * @return string[]
     */
    public function all()
    {
        return \Config::get('auth.socialite.providers');
    }

    /**
     * Get a list of required providers
     * @return string[]
     */
    public function required()
    {
        return \Config::get('auth.socialite.required');
    }

    /**
     * Get a list of optional providers
     * @return string[]
     */
    public function optional()
    {
        return array_diff($this->all(), $this->required());
    }
}
