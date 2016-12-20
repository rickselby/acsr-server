<?php

namespace App\Services;

use App\Contracts\VoiceServerContract;
use App\Services\Socialite\UserProviderStore;

class UserService
{
    /** @var VoiceServerContract */
    protected $voiceServer;

    /** @var UserProviderStore */
    protected $userProvider;

    public function __construct(VoiceServerContract $voiceServer, UserProviderStore $userProvider)
    {
        $this->voiceServer = $voiceServer;
        $this->userProvider = $userProvider;
    }

    /**
     * Update all users' names (and their on_server status) from the discord server
     */
    public function updateNames()
    {
        \DB::table('users')->update(['on_server' => 0]);

        foreach($this->voiceServer->getMembers() AS $id => $name)
        {
            $userProvider = $this->userProvider->getByProvider('discord', $id);
            if ($userProvider) {
                $userProvider->user->name = $name;
                $userProvider->user->new = false;
                $userProvider->user->on_server = true;
                $userProvider->user->save();
            }
        }
    }


}
