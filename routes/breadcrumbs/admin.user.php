<?php

Breadcrumbs::register('admin.user.index', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('User Management', route('admin.user.index'));
});

Breadcrumbs::register('admin.user.edit', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \App\Models\User $user) {
    $breadcrumbs->parent('admin.user.index');
    $breadcrumbs->push('Manage '.$user->name, route('admin.user.edit', $user));
});
