<?php

namespace App\Services\Socialite\Providers;

use SocialiteProviders\Discord\Provider;
use SocialiteProviders\Manager\OAuth2\User;

/**
 * Overwriting the base discord provider as we don't care about email addresses
  */
class Discord extends Provider
{
    /**
     * {@inheritdoc}
     */
    protected $scopes = [
        'identify',
    ];

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => sprintf('%s#%d', $user['username'], $user['discriminator']),
            'name' => $user['username'],
            'avatar' => (is_null($user['avatar'])) ? null : sprintf('https://discordcdn.com/avatars/%s/%s.jpg', $user['id'], $user['avatar']),
        ]);
    }

}
