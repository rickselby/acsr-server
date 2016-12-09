<?php

namespace App\Services\VoiceServer;

use App\Contracts\VoiceServerContract;
use App\Services\VoiceServer\Discord\DiscordApi;

class DiscordVoiceServer implements VoiceServerContract
{
    /** @var string Guild ID to manage */
    protected $guild;

    /** @var DiscordApi */
    protected $discord;

    public function __construct(DiscordApi $discordApi)
    {
        $this->discord = $discordApi;
        $this->guild = env('DISCORD_GUILD_ID');
    }

    /**
     * @inheritdoc
     */
    public function createGroup(string $name, array $users)
    {
        // Create the new role
        $role = $this->discord->createRole(
            $this->guild,
            $name,
            null,
            null,
            null,
            null,
            true // mentionable
        );

        // Add the people to the role
        foreach($users AS $user) {
            $this->discord->addMemberToRole($this->guild, $user->discord_id, $role->id);
        }

        return $role->id;
    }

    /**
     * @inheritdoc
     */
    public function destroyGroup(string $groupID)
    {
        $this->discord->deleteRole($this->guild, $groupID);
    }

    /**
     * @inheritdoc
     */
    public function createVoiceChannel(string $name, array $groupIDs)
    {
        // Get the channel
        $channel = $this->discord->createVoiceChannel($this->guild, $name);

        // Deny @everyone access to the channel
        $this->discord->denyRoleFromVoiceChannel(
            $channel->id,
            $this->discord->getEveryoneRoleID($this->guild)
        );

        // Allow the given groups access to the channel
        foreach($groupIDs AS $groupID) {
            $this->discord->assignRoleToVoiceChannel($channel->id, $groupID);
        }

        return $channel->id;
    }

    /**
     * @inheritdoc
     */
    public function destroyVoiceChannel(string $channelID)
    {
        $this->discord->deleteChannel($channelID);
    }

    /**
     * @inheritdoc
     */
    public function postAnnoucement(string $text)
    {
        $channelName = 'Announcements';

        $channel = $this->discord->findChannel($this->guild, $channelName);
        if (!$channel) {
            $channel = $this->discord->createTextChannel($this->guild, $channelName)->id;
        }

        $this->discord->messageChannel($channel, $text);
    }

    /**
     * @inheritdoc
     */
    public function getMembers()
    {
        $members = [];
        foreach($this->discord->getMembers($this->guild) AS $member) {
            $members[$member->user->id] = isset($member->nick) ? $member->nick : $member->user->username;
        }

        return $members;
    }
}