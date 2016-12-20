@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            {{ $event->name }}
        </h1>
    </div>
@endsection

@section('content')

    @if (!$valid)
        <div class="alert alert-danger">
            <strong>Warning!</strong> The options you have selected for this event are invalid - we cannot generate grids
            for these options. Please mange changes to the options:
            <a class="btn btn-small btn-primary"
               href="{{ route('admin.event.edit', $event) }}">Edit Event</a>
        </div>
    @else
        <p>
            <a class="btn btn-small btn-warning"
               href="{{ route('admin.event.edit', $event) }}">Edit Event</a>
        </p>
    @endif

    <div class="panel {{ $event->config ? 'panel-success' : 'panel-danger' }}">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" href="#config">
                    Server Config
                </a> <span class="caret"></span>
            </h3>
        </div>
        <div class="panel-collapse collapse {{ $event->config ? '' : 'in' }}" id="config" role="tabpanel">
            <div class="panel-body">

                <p>
                    Enter the standard event config here (track, time, weather, etc). Certain things will be overridden
                    by each race; the server name, password and race laps.
                </p>

                {{ Form::open(['route' => ['admin.event.config', $event]]) }}
                    {{ Form::textarea('config', $event->config, ['class' => 'form-control']) }}
                    <br />
                    {{ Form::submit('Update Server Config', ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}

            </div>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" href="#signups">
                    Signups
                    <span class="badge">{{ count($event->signups) }}</span>
                </a> <span class="caret"></span>
            </h3>
        </div>
        <div class="panel-collapse collapse {{ $event->config ? '' : 'in' }}" id="signups" role="tabpanel">
            <div class="panel-body">
                <p>
                    This is a list of the users that have signed up to your event already.
                </p>
                <p>
                    Approximately half an hour before the event starts, the servers will be created, and a cap will be set
                    on the number of signups.
                </p>
                <h4>Users</h4>
                <ul class="list-group">
                    @forelse($event->signups AS $user)
                        <li class="list-group-item">
                            {{ $user->name }}
                        </li>
                    @empty
                        <li class="list-group-item">
                            No users.
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>


@endsection