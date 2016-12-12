@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Your logins
        </h1>
    </div>

@endsection

@section('content')

    @if ($user->new)
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Welcome!</h3>
            </div>
            <div class="panel-body">
                Thanks for signing up! You have been assigned a random name for now; once you
                have linked the required logins (see below) and joined the discord server, your
                nickname on the server will be used instead. It may not update instantly.
                This message will self-destruct when we pick up your username.
            </div>
        </div>
    @endif

    <div class="panel panel-default">
        <div class="panel-body">
            <strong>The information on this page is private.</strong>
            The only user information shown on the site will be your nickname from the
            discord server and your discord avatar.
        </div>
        <div class="panel-body">
            We require you to log in with Discord and Steam for events; your Steam ID is needed
            to assign you to races in Assetto Corsa, and your Discord ID is needed to assign
            you to the relevant groups in Discord.
        </div>
        <div class="panel-body">
            <strong>We only request information from the providers when you log in.</strong>
            Once you've logged in, we have access to your ID, and don't need anything else.
            It's possible your username or avatar is out-of-date below; if so, click the 'update' button,
            which will request you login with that provider, and allow us your latest data.
        </div>
    </div>

    <h2>Required logins</h2>
    <p>
        These logins are required before you can enter events.
    </p>
    <ul class="list-group">
        @foreach(\AuthProviders::required() AS $provider)
            <li class="list-group-item container {{ $user->getProvider($provider) ? 'list-group-item-success' : 'list-group-item-danger' }}">
                @include('user.login-row')
            </li>
        @endforeach
    </ul>

    <h2>Optional logins</h2>
    <p>
        If you prefer to log in to the site with one of these providers, you may do so.
    </p>
    <ul class="list-group">
        @foreach(\AuthProviders::optional() AS $provider)
            <li class="list-group-item container {{ $user->getProvider($provider) ? 'list-group-item-success' : '' }}">
                @include('user.login-row')
            </li>
        @endforeach
    </ul>



@endsection