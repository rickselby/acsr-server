<?php

namespace App\Contracts;

/**
 * Contract for managing the voice chat server
 */
interface VoiceServerContract
{
    /**
     * Set up the server we are using. Details is an array to allow
     * whatever required parameters to be passed.
     * @param array $details
     * @return bool
     */
    public function setServer(array $details);

    /**
     * Create a new group of users
     * @param string $name
     * @param array $users
     * @return int|false
     */
    public function createGroup(string $name, array $users);

    /**
     * Delete a group
     * @param int $groupID
     * @return bool
     */
    public function destroyGroup(int $groupID);

    /**
     * Create a voice channel for the given groups
     * @param string $name
     * @param int[] $groupIDs
     * @return int|false
     */
    public function createVoiceChannel(string $name, array $groupIDs);

    /**
     * Delete a voice channel
     * @param int $channelID
     * @return bool
     */
    public function destroyVoiceChannel(int $channelID);

    /**
     * Post an announcement
     * @param string $text
     * @return bool
     */
    public function postAnnoucement(string $text);
}
