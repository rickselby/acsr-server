<div class="panel panel-info">
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
                    <th>Fastest Lap</th>
                </tr>
                </thead>
                <tbody>
                @foreach($heatStandings AS $standing)
                    <tr>
                        <th>{{ $standing['position'] }}</th>
                        <td>{{ $standing['user']->name }}</td>
                        <td>{{ count($standing['positions']) }}</td>
                        <td>{{ $standing['points'] }}</td>
                        <td>{{ $standing['fastestLap'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>