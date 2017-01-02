@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            {{ $event->name }}
        </h1>
    </div>
@endsection

@section('content')

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" href="#heats">
                    Heats
                </a> <span class="caret"></span>
            </h3>
        </div>
        <div class="panel-collapse collapse in" id="heats" role="tabpanel">
            <div class="panel-body">

                @foreach($event->races->where('heat', true) AS $race)

                    @include('event.race')

                @endforeach

            </div>
        </div>
    </div>

    <div class="panel {{ $event->races->where('heat', true)->count() == $event->races->where('heat', true)->where('complete', true)->count()
                            ? 'panel-info' : 'panel-warning' }}">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" href="#heat-standings">
                    Heat Standings
                </a> <span class="caret"></span>
            </h3>
        </div>
        <div class="panel-collapse collapse" id="heat-standings" role="tabpanel">
            <div class="panel-body">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Pos</th>
                        <th>Driver</th>
                        <th>Races</th>
                        <th>Points</th>
                        <th>Finishes</th>
                        <th>Fastest Lap</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($heatStandings AS $standing)
                        <tr>
                            <th>{{ $standing['position'] }}</th>
                            <td>{{ $standing['user']->name }}</td>
                            <td>{{ $standing['positions']->count() }}</td>
                            <td>{{ $standing['points'] }}</td>
                            <td>{{ $standing['positions']->sort()->implode(', ') }}</td>
                            <td>{{ \Times::toString($standing['fastestLap']) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" href="#finals">
                    Finals
                </a> <span class="caret"></span>
            </h3>
        </div>
        <div class="panel-collapse collapse in" id="finals" role="tabpanel">
            <div class="panel-body">

                @foreach($event->races->where('heat', false) AS $race)

                    @include('event.race')

                @endforeach

            </div>
        </div>
    </div>


@endsection