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

    <ul class="list-group">
        @forelse($open AS $event)
            <li class="list-group-item container-fluid {{ $event->signed_up ? 'list-group-item-success' : '' }}">
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
                                @if ($event->signed_up)
                                    {{ Form::open(['route' => ['event.signup.cancel', $event]]) }}
                                        <strong style="padding-right: 1em;">
                                            Signed up
                                        </strong>
                                        {{ Form::submit('Cancel', ['class' => 'btn btn-warning']) }}
                                    {{ Form::close() }}
                                @else
                                    {{ Form::open(['route' => ['event.signup', $event]]) }}
                                        @if ($event->max_slots)
                                            <strong style="padding-right: 1em;">
                                                {{ $event->max_slots  - $event->signups->count() }}
                                                slots remaining
                                            </strong>
                                        @endif
                                        @if (\Auth::user()->hasRequiredProviders())
                                            {{ Form::submit('Sign Up', ['class' => 'btn btn-primary']) }}
                                        @else
                                            <a href="{{ route('user.logins') }}" class="btn btn-danger">
                                                Missing Logins
                                            </a>
                                        @endif
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