<?php

Breadcrumbs::register('user.logins', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Your Logins', route('user.logins'));
});

Breadcrumbs::register('user.settings', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push('Your Settings', route('user.settings'));
});
