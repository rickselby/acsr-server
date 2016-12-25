<div class="panel {{ $event->races->count() ? 'panel-info' : 'panel-primary' }}">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a role="button" data-toggle="collapse" href="#grids">
                Generate Heat Grids
            </a> <span class="caret"></span>
        </h3>
    </div>
    <div class="panel-collapse collapse {{ $event->races->count() ? '' : 'in' }}" id="grids" role="tabpanel">
        <div class="panel-body">

            {{ Form::open(['route' => ['admin.event.dashboard.grids', $event]]) }}
            @if ($event->races->count())
                {{ Form::submit('Regenerate Grids', ['class' => 'btn btn-info']) }}
            @else
                {{ Form::submit('Generate Grids', ['class' => 'btn btn-primary']) }}
            @endif
            {{ Form::close() }}

        </div>
    </div>
</div>
