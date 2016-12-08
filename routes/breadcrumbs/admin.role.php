<?php

Breadcrumbs::register('admin.role.index', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Role Management', route('admin.role.index'));
});

Breadcrumbs::register('admin.role.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.role.index');
    $breadcrumbs->push('Create', route('admin.role.create'));
});

Breadcrumbs::register('admin.role.show', function($breadcrumbs, \Spatie\Permission\Models\Role $role) {
    $breadcrumbs->parent('admin.role.index');
    $breadcrumbs->push($role->name, route('admin.role.show', $role));
});

Breadcrumbs::register('admin.role.edit', function($breadcrumbs, \Spatie\Permission\Models\Role $role) {
    $breadcrumbs->parent('admin.role.show', $role);
    $breadcrumbs->push('Update', route('admin.role.edit', $role));
});
