<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventAdminPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability, $arguments)
    {
        if ($user->can('event-admin')) {
            return true;
        }
    }

    public function index(User $user)
    {
        return $user->can('event-create');
    }

    public function create(User $user)
    {
        return $user->can('event-create');
    }

    public function manage(User $user, Event $event)
    {
        return $event->isAdmin($user);
    }
}
