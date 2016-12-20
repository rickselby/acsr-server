
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Start Heats
        </h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => ['admin.event.dashboard.start-heats', $event]]) !!}
            {!! Form::submit('Start Heats', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>
