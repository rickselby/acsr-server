<?php

namespace App\Services\Socialite\Auth\ProviderFields;

use SocialiteProviders\Manager\OAuth2\User;

class Provider
{
    public function getProviderFields(User $user)
    {
        return [
            'name' => $user->name,
            'avatar' => $user->avatar,
            'provider_user_id' => $user->id,
        ];
    }
}
