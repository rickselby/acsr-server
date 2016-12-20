<?php

namespace App\Contracts;

/**
 * Contract for managing the voice chat server
 */
interface VoiceServerContract
{
    /**
     * Create a new group of users
     * @param string $name
     * @param array $users
     * @return string|false
     */
    public function createGroup(string $name, array $users);

    /**
     * Delete a group
     * @param string $groupID
     * @return bool
     */
    public function destroyGroup(string $groupID);

    /**
     * Add more users to a group
     * @param string $groupID
     * @param array $users
     * @return bool
     */
    public function addToGroup(string $groupID, array $users);

    /**
     * Create a voice channel for the given groups
     * @param string $name
     * @param string[] $groupIDs
     * @return string|false
     */
    public function createVoiceChannel(string $name, array $groupIDs);

    /**
     * Delete a voice channel
     * @param string $channelID
     * @return bool
     */
    public function destroyVoiceChannel(string $channelID);

    /**
     * Post an announcement
     * @param string $text
     * @return bool
     */
    public function postAnnoucement(string $text);

    /**
     * Post a log message
     * @param string $text
     * @return bool
     */
    public function postLog(string $text);

    /**
     * Get a list of members of the server
     * @return string[]
     */
    public function getMembers();
}
