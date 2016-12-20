<?php

Breadcrumbs::register('home', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});

Breadcrumbs::register('event.index', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Events', route('event.index'));
});

include('breadcrumbs/admin.event.php');
include('breadcrumbs/admin.points-sequences.php');
include('breadcrumbs/admin.role.php');
include('breadcrumbs/admin.user.php');
include('breadcrumbs/user.php');
