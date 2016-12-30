<?php

namespace App\Services\VoiceServer\Discord;

use Httpful\Mime;
use Httpful\Request;
use Httpful\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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

    /** @var array A list of rate limited URLs and their details */
    private $rateLimited = [];

    /** @var Logger */
    private $log;

    /**
     * DiscordApi constructor.
     * Ensure the BOT_KEY is set and
     */
    public function __construct()
    {
        if (!env('DISCORD_BOT_KEY')) {
            throw new \Exception('Please set DISCORD_BOT_KEY before using Discord');
        }

        $template = Request::init()
            ->addHeader('Authorization', 'Bot '.env('DISCORD_BOT_KEY'));
        Request::ini($template);

        $this->log = new Logger('discord_api');
        $this->log->pushHandler(new StreamHandler(storage_path('logs/discord.log')));
    }

    /**
     * Create a new role
     *
     * @param $guildID
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

        $role = $this->post('/guilds/'.$guildID.'/roles', []);

        // Insert the new role ID bottom-last
        $roleList[1] = $role->body->id;

        $this->updateRolePositions($guildID, $roleList);

        return $this->updateRole($guildID, $role->body->id, $name, $permissions, $position, $color, $hoist, $mentionable);
    }

    /**
     * Get the list of roles
     * @param $guildID
     * @return array|object|string
     */
    public function getRoles($guildID)
    {
        return $this->get('/guilds/'.$guildID.'/roles')->body;
    }

    /**
     * Update the role positions to match their place in the given array
     *
     * @param int $guildID
     * @param int[] $orderedPositions Array of Role IDs in REVERSE order (@everyone at the top)
     *
     * @return Response
     */
    public function updateRolePositions($guildID, $orderedPositions)
    {
        $rolePositions = [];
        $pos = 0;

        foreach($orderedPositions AS $key => $roleID) {
            $rolePositions[$pos] = [
                'id' => $roleID,
                'position' => $pos++
            ];
        }

        return $this->patch('/guilds/'.$guildID.'/roles', $rolePositions);
    }

    /**
     * Update an existing role
     *
     * @param int $guildID
     * @param int $roleID
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
        $role = $this->patch(
            '/guilds/'.$guildID.'/roles/'.$roleID,
            $this->removeNulls([
                'name' => $name,
                'permissions' => $permissions,
                'position' => $position,
                'color' => $color,
                'hoist' => $hoist,
                'mentionable' => $mentionable
            ]),
            '/guilds/'.$guildID.'/roles'
        );
        return $role->body;
    }

    /**
     * Delete a role
     *
     * @param int $guildID
     * @param int $roleID
     *
     * @return object
     */
    public function deleteRole($guildID, $roleID)
    {
        $role = $this->delete('/guilds/'.$guildID.'/roles/'.$roleID, '/guilds/'.$guildID.'/roles');
        return $role->body;
    }

    /**
     * Get details for a user
     *
     * @param int $guildID
     * @param int $userID
     *
     * @return object
     */
    public function getMember($guildID, $userID)
    {
        return $this->get('/guilds/'.$guildID.'/members/'.$userID, '/guilds/'.$guildID.'/members')->body;
    }

    /**
     * Add a user to a role
     *
     * @param int $guildID
     * @param int $userID
     * @param int $roleID
     */
    public function addMemberToRole($guildID, $userID, $roleID)
    {
        // Get the users' current roles
        $roles = $this->getMember($guildID, $userID)->roles;
        // Add the new role
        $roles[] = $roleID;
        // Set the roles
        $this->patch('/guilds/'.$guildID.'/members/'.$userID, [
            'roles' => array_unique($roles)
        ], '/guilds/'.$guildID.'/members');
    }

    /**
     * Get the role ID for the @everyone group
     * @param int $guildID
     * @return int|null
     */
    public function getEveryoneRoleID($guildID)
    {
        $roles = $this->get('/guilds/'.$guildID.'/roles');
        foreach($roles->body AS $role) {
            if ($role->name == '@everyone') {
                return $role->id;
            }
        }
        return null;
    }

    /**
     * Find a channel by name
     *
     * @param int $guildID
     * @param string $name
     *
     * @return int|null
     */
    public function findChannel($guildID, $name)
    {
        $channels = $this->get('/guilds/'.$guildID.'/channels');
        foreach($channels->body AS $channel) {
            if ($channel->name == strtolower($name)) {
                return $channel->id;
            }
        }
        return null;
    }

    /**
     * Create a new voice channel
     *
     * @param int $guildID
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
     * @param int $guildID
     * @param string $name
     *
     * @return object
     */
    public function createTextChannel($guildID, $name)
    {
        return $this->createChannel($guildID, $name, 'text');
    }

    /**
     * Assign a role to a voice channel
     *
     * @param $channelID
     * @param $roleID
     */
    public function assignRoleToVoiceChannel($channelID, $roleID)
    {
        $this->put('/channels/'.$channelID.'/permissions/'.$roleID, [
            'type' => 'role',
            'allow' => DiscordPermissions::CONNECT,
            'deny' => 0,
        ], '/channels/'.$channelID.'/permissions');
    }

    /**
     * Deny a role access to a voice channel
     *
     * @param $channelID
     * @param $roleID
     */
    public function denyRoleFromVoiceChannel($channelID, $roleID)
    {
        $this->put('/channels/'.$channelID.'/permissions/'.$roleID, [
            'type' => 'role',
            'deny' => DiscordPermissions::CONNECT,
            'allow' => 0,
        ], '/channels/'.$channelID.'/permissions');
    }

    /**
     * Create a new channel
     *
     * @param int $guildID
     * @param string $name
     * @param string $type
     *
     * @return object
     */
    private function createChannel($guildID, $name, $type)
    {
        return $this->post('/guilds/'.$guildID.'/channels', [
            'name' => $name,
            'type' => $type
        ])->body;
    }

    /**
     * Delete a channel
     *
     * @param $channelID
     *
     * @return object
     */
    public function deleteChannel($channelID)
    {
        return $this->delete('/channels/'.$channelID)->body;
    }

    /**
     * Send a message to a channel
     *
     * @param int $channelID
     * @param string $message
     *
     * @return object
     */
    public function messageChannel($channelID, $message)
    {
        return $this->post('/channels/'.$channelID.'/messages', [
            'content' => $message
        ])->body;
    }

    /**
     * Get the list of members of the given guild
     *
     * @param int $guildID
     *
     * @return []
     */
    public function getMembers($guildID)
    {
        // TODO: make this check if we have 1000 and send another request...
        return $this->get('/guilds/'.$guildID.'/members?limit=1000', '/guilds/'.$guildID.'/members')->body;
    }

    /**************************************************************************
     * Private stuff from here
     *************************************************************************/

    /**
     * Execute a delete request
     *
     * @param string $url
     *
     * @return Response
     */
    private function delete($url, $rate_uri = null)
    {
        return $this->send(
            Request::delete(self::BASE_URL.$url),
            [],
            $rate_uri
        );
    }

    /**
     * Execute a get request
     *
     * @param string $url
     *
     * @return Response
     */
    private function get($url, $rate_uri = null)
    {
        return $this->send(
            Request::get(self::BASE_URL.$url),
            [],
            $rate_uri
        );
    }

    /**
     * Execute a patch request
     *
     * @param string $url
     * @param array $body
     *
     * @return Response
     */
    private function patch($url, $body, $rate_uri = null)
    {
        return $this->send(
            Request::patch(self::BASE_URL.$url),
            $body,
            $rate_uri
        );
    }

    /**
     * Execute a post request
     *
     * @param string $url
     * @param array $body
     *
     * @return Response
     */
    private function post($url, $body, $rate_uri = null)
    {
        return $this->send(
            Request::post(self::BASE_URL.$url),
            $body,
            $rate_uri
        );
    }

    /**
     * Execute a put request
     *
     * @param string $url
     * @param array $body
     *
     * @return Response
     */
    private function put($url, $body, $rate_uri = null)
    {
        return $this->send(
            Request::put(self::BASE_URL.$url),
            $body,
            $rate_uri
        );
    }

    /**
     * Attatch a body to a request and send the request
     *
     * @param Request $request
     * @param array $body
     *
     * @return Response
     */
    private function send(Request $request, $body, $rate_uri)
    {
        $this->log->info('Send', [
            'method' => $request->method,
            'uri' => $request->uri
        ]);

        if (count($body)) {
            $request->sendsType(Mime::JSON)
                ->body(json_encode($body));
        }

        $request->rate_uri = $rate_uri;

        return $this->checkForErrors(
            $this->rateLimit(
                $request
            )
        );
    }

    /**
     * Send the request, but take into account rate limiting
     *
     * @param $request
     *
     * @return mixed
     */
    private function rateLimit($request)
    {
        /**
         * Adding the method to the URI for a unique rate limit is not quite right. See
         * https://github.com/hammerandchisel/discord-api-docs/issues/190 for updates.
         */
        $rateURI = $request->method.($request->rate_uri ?? $request->uri);
        // Check if we need to wait before sending this request
        if (isset($this->rateLimited[$rateURI])) {
            // might have to wait here?
            if ($this->rateLimited[$rateURI]['remaining'] == 0) {
                // yes, we need to wait
                $time = $this->rateLimited[$rateURI]['reset'] - time();
                if ($time > 0) {
                    $this->log->info('Rate Limited', [
                        'uri' => $rateURI,
                        'time' => $time,
                    ]);
                    sleep($time);
                }
            }
        }
        // Send the request!
        $response = $request->send();
        // Check if we need to update the rate limiting stuff
        if ($response->headers->offsetExists('x-ratelimit-reset')) {
            $this->rateLimited[$rateURI] = [
                'remaining' => $response->headers->offsetGet('x-ratelimit-remaining'),
                'reset' => $response->headers->offsetGet('x-ratelimit-reset'),
            ];
            $this->log->info('Got Limit', [
                'uri' => $rateURI,
                'remaining' => $response->headers->offsetGet('x-ratelimit-remaining'),
                'reset' => $response->headers->offsetGet('x-ratelimit-reset'),
                'time' => $response->headers->offsetGet('x-ratelimit-reset') - time(),
            ]);
        }
        return $response;
    }

    /**
     * Check a request for errors; throw exceptions if halting ones found
     *
     * @param Response $response
     *
     * @return Response
     */
    private function checkForErrors(Response $response)
    {
        switch($response->code) {
            case '400':
                throw new \Exception('Discord: Bad Request');
            case '401':
                throw new \Exception('Discord: Unauthorized');
            case '403':
                throw new \Exception('Discord: Permission Denied');
            case '404':
                throw new \Exception('Discord: 404 Not Found');
            case '405':
                throw new \Exception('Discord: Method Not Allowed');
            case '429':
                throw new \Exception('Discord: Too Many Requests');
            default:
        }

        return $response;
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
}
