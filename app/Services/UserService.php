<?php

namespace App\Services;

use App\Contracts\VoiceServerContract;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\Socialite\UserProviderStore;
use Nubs\RandomNameGenerator\Alliteration;

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
     * Create a dummy user (heh)
     *
     * @return User
     */
    public function create()
    {
        $generator = new Alliteration();

        $user = User::create([
            'name' => $generator->getName()
        ]);

        return $user;
    }

    /**
     * Update a user from a request
     *
     * @param UserRequest $request
     * @param User $user
     */
    public function update(UserRequest $request, User $user)
    {
        $user->fill($request->all());
        $user->save();
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
