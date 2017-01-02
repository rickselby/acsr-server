@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            {{ $event->name }} Dashboard
        </h1>
    </div>
@endsection

@section('content')

    @include('admin.event.dashboard.servers')

    @if ($sections['signups'])
        @include('admin.event.dashboard.signups')
    @endif

    @if ($sections['grids'])
        @include('admin.event.dashboard.grids')
    @endif

    @if ($sections['races'])
        @include('admin.event.dashboard.races')
    @endif

    @if ($sections['start-heats'])
        @include('admin.event.dashboard.start-heats')
    @endif

    @if ($sections['run-next-session'])
        @include('admin.event.dashboard.run-next-session')
    @endif

    @if ($sections['heat-standings'])
        @include('admin.event.dashboard.heat-standings')
    @endif

    @if ($sections['start-finals'])
        @include('admin.event.dashboard.start-finals')
    @endif

@endsection
