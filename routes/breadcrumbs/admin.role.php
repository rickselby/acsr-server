<?php

Breadcrumbs::register('admin.role.index', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Role Management', route('admin.role.index'));
});

Breadcrumbs::register('admin.role.create', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs) {
    $breadcrumbs->parent('admin.role.index');
    $breadcrumbs->push('Create', route('admin.role.create'));
});

Breadcrumbs::register('admin.role.show', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \Spatie\Permission\Models\Role $role) {
    $breadcrumbs->parent('admin.role.index');
    $breadcrumbs->push($role->name, route('admin.role.show', $role));
});

Breadcrumbs::register('admin.role.edit', function(\DaveJamesMiller\Breadcrumbs\Generator $breadcrumbs, \Spatie\Permission\Models\Role $role) {
    $breadcrumbs->parent('admin.role.show', $role);
    $breadcrumbs->push('Update', route('admin.role.edit', $role));
});
