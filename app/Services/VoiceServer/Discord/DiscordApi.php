<?php

namespace App\Services\VoiceServer\Discord;

use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;

/**
 * A simple REST API interface for some discord functionality that we need.
 * I considered using DiscordPHP, but it's focused around being a bot, and not
 * really suited to simple server management.
 *
 * @package App\Services\VoiceServer\Discord
 */
class DiscordApi
{
    const BASE_URL = 'https://discordapp.com/api';

    /** @var DiscordRequest */
    protected $discordRequest;

    /**
     * DiscordApi constructor.
     * Ensure the BOT_KEY is set and
     */
    public function __construct(DiscordRequest $discordRequest)
    {
        if (!env('DISCORD_BOT_KEY')) {
            throw new \Exception('Please set DISCORD_BOT_KEY before using Discord');
        }

        $template = Request::init()
            ->addHeader('Authorization', 'Bot '.env('DISCORD_BOT_KEY'));
        Request::ini($template);

        $this->discordRequest = $discordRequest;
    }

    /**
     * Create a new role
     *
     * @param string $guildID
     * @param string|null $name
     * @param int|null $permissions
     * @param int|null $position
     * @param int|null $color
     * @param bool|null $hoist
     * @param bool|null $mentionable
     *
     * @return object
     */
    public function createRole($guildID, $name = null, $permissions = null, $position = null, $color = null, $hoist = null, $mentionable = null)
    {
        // Get the current list of roles and their positions
        $roleList = $this->getRoles($guildID);
        // Sort by current position
        usort($roleList, function($a, $b) {
            return $a->position - $b->position;
        });
        $roleList = array_map(function($a) {
            return $a->id;
        }, $roleList);

        // Copy the first element on to the front of the list
        array_unshift($roleList, $roleList[0]);
        // Then unset the 'old' first element, leaving a space for the new role
        $roleList[1] = [];

        $response = $this->discordRequest->send(
            Request::post(self::BASE_URL.'/guilds/'.$guildID.'/roles'),
            'UPDATE/guilds/'.$guildID.'/roles'
        );

        // Insert the new role ID bottom-last
        $roleList[1] = $response->body->id;

        $this->updateRolePositions($guildID, $roleList);

        return $this->updateRole($guildID, $response->body->id, $name, $permissions, $position, $color, $hoist, $mentionable);
    }

    /**
     * Get the list of roles
     *
     * @param string $guildID
     *
     * @return array|object|string
     */
    private function getRoles($guildID)
    {
        return $this->discordRequest->send(
            Request::get(self::BASE_URL.'/guilds/'.$guildID.'/roles'),
            'GET/guilds/'.$guildID.'/roles'
        )->body;
    }

    /**
     * Update the role positions to match their place in the given array
     *
     * @param string $guildID
     * @param string[] $orderedPositions Array of Role IDs in REVERSE order (@everyone at the top)
     *
     * @return Response
     */
    private function updateRolePositions($guildID, $orderedPositions)
    {
        $rolePositions = [];
        $pos = 0;

        foreach($orderedPositions AS $key => $roleID) {
            $rolePositions[$pos] = [
                'id' => $roleID,
                'position' => $pos++
            ];
        }

        return $this->discordRequest->send(
            Request::patch(self::BASE_URL.'/guilds/'.$guildID.'/roles')
                ->sendsType(Mime::JSON)
                ->body(json_encode($rolePositions)),
            'UPDATE/guilds/'.$guildID.'/roles'
        );
    }

    /**
     * Update an existing role
     *
     * @param string $guildID
     * @param string $roleID
     * @param string|null $name
     * @param int|null $permissions
     * @param int|null $position
     * @param int|null $color
     * @param bool|null $hoist
     * @param bool|null $mentionable
     *
     * @return object
     */
    public function updateRole($guildID, $roleID, $name = null, $permissions = null, $position = null, $color = null, $hoist = null, $mentionable = null)
    {
        return $this->discordRequest->send(
            Request::patch(self::BASE_URL.'/guilds/'.$guildID.'/roles/'.$roleID)
                ->sendsType(Mime::JSON)
                ->body(json_encode($this->removeNulls([
                    'name' => $name,
                    'permissions' => $permissions,
                    'position' => $position,
                    'color' => $color,
                    'hoist' => $hoist,
                    'mentionable' => $mentionable
                ]))),
            'UPDATE/guilds/'.$guildID.'/roles'
        )->body;
    }

    /**
     * Delete a role
     *
     * @param string $guildID
     * @param string $roleID
     *
     * @return object
     */
    public function deleteRole($guildID, $roleID)
    {
        return $this->discordRequest->send(
            Request::delete(self::BASE_URL.'/guilds/'.$guildID.'/roles/'.$roleID),
            'UPDATE/guilds/'.$guildID.'/roles'
        )->body;
    }

    /**
     * Add a user to a role
     *
     * @param string $guildID
     * @param string $userID
     * @param string $roleID
     */
    public function addMemberToRole($guildID, $userID, $roleID)
    {
        $this->discordRequest->send(
            Request::put(self::BASE_URL.'/guilds/'.$guildID.'/members/'.$userID.'/roles/'.$roleID),
            'UPDATE/guilds/'.$guildID.'/members'
        );
    }

    /**
     * Get the role ID for the @everyone group
     *
     * @param string $guildID
     *
     * @return string|null
     */
    public function getEveryoneRoleID($guildID)
    {
        return \Cache::remember('discord-everyone-role-id', 1, function() use ($guildID) {
            return $this->getRoleIDByName($guildID, '@everyone');
        });
    }

    /**
     * Look up a role ID by name
     *
     * @param $guildID
     * @param $name
     *
     * @return string|null
     */
    public function getRoleIDByName($guildID, $name)
    {
        $roles = $this->discordRequest->send(
            Request::get(self::BASE_URL.'/guilds/'.$guildID.'/roles'),
            'GET/guilds/'.$guildID.'/roles'
        );

        foreach($roles->body AS $role) {
            if ($role->name == $name) {
                return $role->id;
            }
        }

        return null;
    }

    /**
     * Find a channel by name
     *
     * @param string $guildID
     * @param string $name
     *
     * @return int|null
     */
    public function findChannel($guildID, $name)
    {
        return \Cache::remember('discord-channel-id-'.$name, 1, function() use ($guildID, $name) {
            $channels = $this->discordRequest->send(
                Request::get(self::BASE_URL . '/guilds/' . $guildID . '/channels'),
                'GET/guilds/' . $guildID . '/channels'
            );

            foreach ($channels->body AS $channel) {
                if ($channel->name == strtolower($name)) {
                    return $channel->id;
                }
            }

            return null;
        });
    }

    /**
     * Create a new voice channel
     *
     * @param string $guildID
     * @param string $name
     *
     * @return object
     */
    public function createVoiceChannel($guildID, $name)
    {
        return $this->createChannel($guildID, $name, 'voice');
    }

    /**
     * Create a new text channel
     *
     * @param string $guildID
     * @param string $name
     *
     * @return object
     */
    public function createTextChannel($guildID, $name)
    {
        return $this->createChannel($guildID, $name, 'text');
    }

    /**
     * Create a new channel
     *
     * @param string $guildID
     * @param string $name
     * @param string $type
     *
     * @return object
     */
    private function createChannel($guildID, $name, $type)
    {
        return $this->discordRequest->send(
            Request::post(self::BASE_URL.'/guilds/'.$guildID.'/channels')
                ->sendsType(Mime::JSON)
                ->body(json_encode([
                    'name' => $name,
                    'type' => $type
                ])),
            'UPDATE/guilds/'.$guildID.'/channels'
        )->body;
    }

    /**
     * Assign a role to a voice channel
     *
     * @param string $channelID
     * @param string $roleID
     */
    public function assignRoleToChannel($channelID, $roleID)
    {
        $this->updateChannelPermissions($channelID, $roleID, [
            'type' => 'role',
            'allow' => DiscordPermissions::CONNECT,
            'deny' => 0,
        ]);
    }

    /**
     * Assign a member to a voice channel
     *
     * @param string $channelID
     * @param string $memberID
     */
    public function assignMemberToChannel($channelID, $memberID)
    {
        $this->updateChannelPermissions($channelID, $memberID, [
            'type' => 'member',
            'allow' => DiscordPermissions::CONNECT,
            'deny' => 0,
        ]);
    }

    /**
     * Deny a role access to a voice channel
     *
     * @param string $channelID
     * @param string $roleID
     */
    public function denyRoleFromChannel($channelID, $roleID)
    {
        $this->updateChannelPermissions($channelID, $roleID, [
            'type' => 'role',
            'deny' => DiscordPermissions::CONNECT,
            'allow' => 0,
        ]);
    }

    /**
     * Assign a member to a voice channel
     *
     * @param string $channelID
     * @param string $memberID
     */
    public function denyMemberFromChannel($channelID, $memberID)
    {
        $this->updateChannelPermissions($channelID, $memberID, [
            'type' => 'member',
            'deny' => DiscordPermissions::CONNECT,
            'allow' => 0,
        ]);
    }

    /**
     * Update permissions for a role on a voice channel
     *
     * @param string $channelID
     * @param string $overwriteID Role ID or User ID
     * @param array $body
     */
    private function updateChannelPermissions($channelID, $overwriteID, $body)
    {
        $this->discordRequest->send(
            Request::put(self::BASE_URL.'/channels/'.$channelID.'/permissions/'.$overwriteID)
                ->sendsType(Mime::JSON)
                ->body(json_encode($body)),
            'UPDATE/channels/'.$channelID.'/permissions'
        );
    }

    /**
     * Delete a channel
     *
     * @param string $channelID
     *
     * @return object
     */
    public function deleteChannel($channelID)
    {
        return $this->discordRequest->send(
            Request::delete(self::BASE_URL.'/channels/'.$channelID),
            'UPDATE/channels/'.$channelID
        )->body;
    }

    /**
     * Send a message to a channel
     *
     * @param string $channelID
     * @param string $message
     *
     * @return object
     */
    public function messageChannel($channelID, $message)
    {
        return $this->discordRequest->send(
            Request::post(self::BASE_URL.'/channels/'.$channelID.'/messages')
                ->sendsType(Mime::JSON)
                ->body(json_encode([
                    'content' => $message
                ])),
            'UPDATE/channels/'.$channelID.'/messages'
        )->body;
    }

    /**
     * Get the list of members of the given guild
     *
     * @param string $guildID
     *
     * @return []
     */
    public function getMembers($guildID)
    {
        // TODO: make this check if we have 1000 and send another request...
        return $this->discordRequest->send(
            Request::get(self::BASE_URL.'/guilds/'.$guildID.'/members?limit=1000'),
            'GET/guilds/'.$guildID.'/members'
        )->body;
    }

    /**
     * Remove null values from an array
     *
     * @param array $options
     *
     * @return array
     */
    private function removeNulls(array $options)
    {
        foreach($options AS $key => $value) {
            if ($value == null) {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * Get the member ID of the bot
     *
     * @return string
     */
    public function getBotID()
    {
        // Cache the bot ID for a minute once we know it
        return \Cache::remember('discord-bot-id', 1, function() {
            return $this->discordRequest->send(
                Request::get(self::BASE_URL . '/users/@me'),
                'GET/users/'
            )->body->id;
        });
    }
}
