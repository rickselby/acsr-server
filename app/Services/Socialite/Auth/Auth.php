<?php

namespace App\Services\Socialite\Auth;

use App\Models\User;
use App\Models\UserProvider;
use App\Services\Socialite\Auth\ProviderFields\Provider;
use App\Services\Socialite\Providers;
use App\Services\Socialite\UserProviderStore;

class Auth
{
    /** @var UserProviderStore */
    protected $userProviderStore;

    /** @var Providers */
    protected $providers;

    public function __construct(UserProviderStore $userProviderStore, Providers $providers)
    {
        $this->userProviderStore = $userProviderStore;
        $this->providers = $providers;
    }

    /**
     * Deal with the return from a provider
     *
     * @param string $provider
     */
    public function handleProviderCallback($provider)
    {
        // Can we get the provider's user?
        try {
            $providerUser = \Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            \Notification::add('error', 'Authorization failed');
            return;
        }

        if (\Auth::check()) {
            // There is a user logged in; try to add these details to the user
            $this->addProvider($provider, $providerUser);
        } else {
            // No user logged in; log in (or create a new user) using these details
            $this->login($provider, $providerUser);
        }
    }

    /**
     * Log a user in from the given provider details, or create a new user if necesssary
     *
     * @param string $provider
     * @param \SocialiteProviders\Manager\OAuth2\User $providerUser
     */
    protected function login($provider, $providerUser)
    {
        $userProvider = $this->userProviderStore->getByProvider($provider, $providerUser->id);
        if ($userProvider) {
            \Auth::login($userProvider->user);
        } else {
            // Create the new user - give them a fake name for now
            $generator = new \Nubs\RandomNameGenerator\Alliteration();

            $user = User::create([
                'name' => $generator->getName()
            ]);

            // Attach the current provider to the user
            $this->attachProvider($user, $provider, $providerUser);

            // Log the user in
            \Auth::login($user);
        }
    }

    /**
     * Add a provider to the current user
     *
     * @param string $provider
     * @param \SocialiteProviders\Manager\OAuth2\User $providerUser
     */
    protected function addProvider($provider, $providerUser)
    {
        $userProvider = $this->userProviderStore->getByProvider($provider, $providerUser->id);

        if ($userProvider) {
            // This login is already tied to a user; is it this user?
            if (\Auth::user() != $userProvider->user) {
                \Notification::add('error', 'The login requested is already linked to an account');
            } else {
                $this->updateProvider($userProvider, $provider, $providerUser);
            }
        } else {
            // Does not exist yet; add to this user
            $this->attachProvider(\Auth::user(), $provider, $providerUser);
        }
    }

    /**
     * Get the fields required for a UserProvider from the given provider
     *
     * @param string $provider
     * @param \SocialiteProviders\Manager\OAuth2\User $providerUser
     * @return array
     */
    protected function getProviderFields($provider, $providerUser)
    {
        // Allow a way of overriding the default field allocation
        // (needed for steam...)
        $class = '\\App\\Services\\Socialite\\Auth\\ProviderFields\\'.ucfirst($provider);

        if (class_exists($class)) {
            $fieldProvider = new $class();
        } else {
            $fieldProvider = new Provider();
        }

        return array_merge(
            $fieldProvider->getProviderFields($providerUser),
            [
                'provider' => $provider
            ]
        );
    }

    /**
     * Attach a provider login to a user
     *
     * @param User $user
     * @param string $provider
     * @param \SocialiteProviders\Manager\OAuth2\User $providerUser
     */
    protected function attachProvider($user, $provider, $providerUser)
    {
        $userProvider = new UserProvider();
        $userProvider->provider = $provider;
        $userProvider->fill($this->getProviderFields($provider, $providerUser));
        $userProvider->user()->associate($user);
        $userProvider->save();
    }

    /**
     * Update details for a provider
     *
     * @param UserProvider $userProvider
     * @param string $provider
     * @param \SocialiteProviders\Manager\OAuth2\User $providerUser
     */
    protected function updateProvider(UserProvider $userProvider, $provider, $providerUser)
    {
        $userProvider->fill($this->getProviderFields($provider, $providerUser));
        $userProvider->save();
    }

    /**
     * Remove the provider link from a user, as long as they will still have one left
     *
     * @param $provider
     */
    public function destroy($provider)
    {
        // Don't delete the last provider
        if (count(\Auth::user()->providers) > 1) {
            $this->userProviderStore->flush(\Auth::user(), $provider);
        } else {
            \Notification::add('error', 'You must have at least one login method for your account');
        }
    }

}
