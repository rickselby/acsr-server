<?php

Breadcrumbs::register('home', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});

Breadcrumbs::register('event.index', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Events', route('event.index'));
});

Breadcrumbs::register('event.show', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \App\Models\Event $event) {
    $breadcrumbs->parent('event.index');
    $breadcrumbs->push($event->name, route('event.show', $event));
});

include('breadcrumbs/admin.event.php');
include('breadcrumbs/admin.points-sequences.php');
include('breadcrumbs/admin.role.php');
include('breadcrumbs/admin.user.php');
include('breadcrumbs/user.php');
