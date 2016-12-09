@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Events
        </h1>
    </div>
@endsection

@section('content')

    <h2>Upcoming Events</h2>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" href="#help">
                    Signup Help
                </a> <span class="caret"></span>
            </h3>
        </div>
        <div class="panel-collapse collapse" id="help" role="tabpanel">
            <div class="panel-body">

                <p>
                    Sign up for an event here. You must be logged in to do so.
                </p>

                Please be in the discord server and connected to the general voice channel at the start time of the event.

            </div>
        </div>
    </div>

    <ul class="list-group">
        @forelse($open AS $event)
            <li class="list-group-item container-fluid {{ $event->signedup ? 'list-group-item-success' : '' }}">
                <div class="row">
                    <div class="row-vertical">
                        <div class="col-sm-4 col-vertical">
                            {{ $event->name }}
                        </div>
                        <div class="col-sm-4 col-vertical">
                            {{ \Times::toUserTimezone($event->start) }}
                        </div>
                        <div class="col-sm-4 col-vertical text-right">
                            @if (\Auth::check())
                                @if ($event->signedup)
                                    {{ Form::open(['route' => ['event.signup.cancel', $event]]) }}
                                        <button class="btn btn-default" disabled="disabled">
                                            Signed up
                                        </button>
                                        {{ Form::submit('Cancel', ['class' => 'btn btn-warning']) }}
                                    {{ Form::close() }}
                                @else
                                    {{ Form::open(['route' => ['event.signup', $event]]) }}
                                        {{ Form::submit('Sign Up', ['class' => 'btn btn-primary']) }}
                                    {{ Form::close() }}
                                @endif
                            @else
                                <button class="btn btn-default" disabled="disabled">
                                    Login to sign up
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item">
                No events.
            </li>
        @endforelse
    </ul>

    <h2>Past events</h2>

    <ul class="list-group">
        @forelse($past AS $event)
            <li class="list-group-item container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        {{ $event->name }}
                    </div>
                    <div class="col-sm-4">
                        {{ \Times::toUserTimezone($event->start) }}
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item">
                No events.
            </li>
        @endforelse
    </ul>

@endsection