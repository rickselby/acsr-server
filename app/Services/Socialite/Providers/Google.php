<?php

namespace App\Services\Socialite\Providers;

use SocialiteProviders\Google\Provider;
use SocialiteProviders\Manager\OAuth2\User;

/**
 * Overwriting the base discord provider as we don't care about email addresses
 */
class Google extends Provider
{
    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'profile',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => array_get($user, 'nickname'),
            'name' => $user['displayName'],
            'avatar' => array_get($user, 'image')['url'],
        ]);
    }


}
