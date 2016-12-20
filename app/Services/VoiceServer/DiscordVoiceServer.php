<?php

namespace App\Services\VoiceServer;

use App\Contracts\VoiceServerContract;
use App\Services\VoiceServer\Discord\DiscordApi;
use Carbon\Carbon;

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

        $this->addToGroup($role->id, $users);

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
    public function addToGroup(string $groupID, array $users)
    {
        // Add the people to the role
        foreach($users AS $user) {
            // Don't try to add people to the group if they're not on the server...
            if ($user->on_server) {
                $this->discord->addMemberToRole(
                    $this->guild,
                    $user->getProvider('discord')->provider_user_id,
                    $groupID
                );
            }
        }
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
        $this->postToChannel('Announcements', $text);
    }

    /**
     * @inheritdoc
     */
    public function postLog(string $text)
    {
        $time = '['.Carbon::now()->toDateTimeString().']';
        $this->postToChannel('log', $time.' '.$text);
    }

    /**
     * Post a message to a channel
     *
     * @param $channel
     * @param $text
     */
    protected function postToChannel($channel, $text)
    {
        $channelID = $this->discord->findChannel($this->guild, $channel);
        if (!$channelID) {
            $channelID = $this->discord->createTextChannel($this->guild, $channel)->id;
        }

        $this->discord->messageChannel($channelID, $text);
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
