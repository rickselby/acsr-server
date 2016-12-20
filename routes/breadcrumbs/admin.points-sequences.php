<?php

Breadcrumbs::register('admin.points-sequence.index', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Points Sequences', route('admin.points-sequence.index'));
});

Breadcrumbs::register('admin.points-sequence.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.points-sequence.index');
    $breadcrumbs->push('Create', route('admin.points-sequence.create'));
});

Breadcrumbs::register('admin.points-sequence.show', function($breadcrumbs, \App\Models\PointsSequence $sequence) {
    $breadcrumbs->parent('admin.points-sequence.index');
    $breadcrumbs->push($sequence->name, route('admin.points-sequence.show', $sequence));
});

Breadcrumbs::register('admin.points-sequence.edit', function($breadcrumbs, \App\Models\PointsSequence $sequence) {
    $breadcrumbs->parent('admin.points-sequence.show', $sequence);
    $breadcrumbs->push('Update', route('admin.points-sequence.edit', $sequence));
});
