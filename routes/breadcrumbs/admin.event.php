<?php

Breadcrumbs::register('admin.event.index', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Event Management', route('admin.event.index'));
});

Breadcrumbs::register('admin.event.create', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('admin.event.index');
    $breadcrumbs->push('Create', route('admin.event.create'));
});

Breadcrumbs::register('admin.event.show', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \App\Models\Event $event) {
    $breadcrumbs->parent('admin.event.index');
    $breadcrumbs->push($event->name, route('admin.event.show', $event));
});

Breadcrumbs::register('admin.event.edit', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \App\Models\Event $event) {
    $breadcrumbs->parent('admin.event.show', $event);
    $breadcrumbs->push('Edit', route('admin.event.edit', $event));
});

Breadcrumbs::register('admin.event.dashboard', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \App\Models\Event $event) {
    $breadcrumbs->parent('admin.event.show', $event);
    $breadcrumbs->push('Dashboard', route('admin.event.dashboard', $event));
});
