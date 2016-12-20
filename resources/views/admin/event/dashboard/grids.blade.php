<div class="panel {{ $event->races->count() ? 'panel-info' : 'panel-primary' }}">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a role="button" data-toggle="collapse" href="#grids">
                Heat Grids
                @if ($event->races->count())
                    <span class="badge">{{ $event->races->count() }}</span>
                @endif
            </a> <span class="caret"></span>
        </h3>
    </div>
    <div class="panel-collapse collapse {{ $event->races->count() ? '' : 'in' }}" id="grids" role="tabpanel">
        <div class="panel-body">

            @if ($event->races->count())
                @for ($page = 1; $page <= ceil($event->races->count() / 4); $page++)
                    <div class="row">
                        @foreach($event->races->forPage($page, 4) AS $race)
                            <div class="col-sm-3">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <strong>
                                            {{ $race->name }}
                                        </strong>
                                    </li>
                                    @foreach($race->entrants()->grid()->get() AS $entrant)
                                        <li class="list-group-item">
                                            {{ $entrant->grid }}
                                            {{ $entrant->user->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endfor

                {{ Form::open(['route' => ['admin.event.dashboard.grids', $event]]) }}
                    {{ Form::submit('Regenerate Grids', ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}
            @else

                {{ Form::open(['route' => ['admin.event.dashboard.grids', $event]]) }}
                    {{ Form::submit('Generate Grids', ['class' => 'btn btn-primary']) }}
                {{ Form::close() }}

            @endif

        </div>
    </div>
</div>
