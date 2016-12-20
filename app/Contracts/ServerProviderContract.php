<?php

namespace App\Contracts;
use App\Models\Event;
use App\Models\Server;

/**
 * Contract for creating and managing an Assetto Corsa server.
 */
interface ServerProviderContract
{
    /**
     * Create a new Assetto Corsa server; return an identifier
     * @param int $eventID ID of the event we're creating a server for
     * @return int|false
     */
    public function create(Event $event);

    /**
     * Destroy a server
     * @param Server $server
     * @return bool
     */
    public function destroy(Server $server);
}