
<div class="panel {{
                        $race->complete
                            ? ((\Auth::check() && $race->users->contains(\Auth::user()))
                                ? 'panel-primary'
                                : 'panel-info')
                            : ($race->active
                                ? 'panel-success'
                                : 'panel-default')
                            }} ">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a role="button" data-toggle="collapse" href="#race-{{ $race->id }}">
                {{ $race->name }}
            </a> <span class="caret"></span>
        </h3>
    </div>
    <div class="panel-collapse collapse" id="race-{{ $race->id }}" role="tabpanel">
        <div class="panel-body">
            @if ($race->complete)
                <h4>
                    Results
                    <a href="{{ route('race.json', $race) }}" role="button" class="btn btn-xs btn-info">JSON</a>
                </h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Pos</th>
                        <th>Driver</th>
                        <th>Time</th>
                        <th>Laps</th>
                        <th>Fastest Lap</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($race->entrants()->results()->get() AS $entrant)
                        <tr>
                            <th>{{ $entrant->position }}</th>
                            <td>{{ $entrant->user->name }}</td>
                            <td>{{ \Times::toString($entrant->time) }}</td>
                            <td>{{ $entrant->laps }}</td>
                            <td>{{ \Times::toString($entrant->fastest_lap) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <h4>Grid</h4>
                <table class="table">
                    <tbody>
                    @foreach($race->entrants()->grid()->get() AS $entrant)
                        <tr>
                            <td class="col-sm-1">{{ $entrant->grid }}</td>
                            <td>{{ $entrant->user->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
