@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Events
        </h1>
    </div>
@endsection

@section('content')

    <p>
        <a class="btn btn-small btn-info" href="{{ route('admin.event.create') }}">Add a new event</a>
    </p>

    <h2>Your Events</h2>

    <ul class="list-group">
        @forelse(\Auth::user()->adminEvents AS $event)
            @include('admin.event.index-event-row')
        @empty
            <li class="list-group-item">
                No events.
            </li>
        @endforelse
    </ul>

    @can('event-admin')
    <h2>All events</h2>

    <ul class="list-group">
        @forelse($events AS $event)
            @include('admin.event.index-event-row')
        @empty
            <li class="list-group-item">
                No events.
            </li>
        @endforelse
    </ul>

    @endcan


@endsection