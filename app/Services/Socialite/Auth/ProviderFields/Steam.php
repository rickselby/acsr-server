<?php

namespace App\Services\Socialite\Auth\ProviderFields;

use SocialiteProviders\Manager\OAuth2\User;

class Steam extends Provider
{
    public function getProviderFields(User $user)
    {
        // We only need to overwrite the name field, so:
        return array_merge(
            parent::getProviderFields($user),
            [
                'name' => $user->nickname,
            ]
        );
    }
}
