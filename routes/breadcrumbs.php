<?php

Breadcrumbs::register('home', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('home'));
});

include('breadcrumbs/admin.role.php');
include('breadcrumbs/admin.user.php');
include('breadcrumbs/user.php');
