
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Start Finals
        </h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => ['admin.event.dashboard.start-finals', $event]]) !!}
            {!! Form::submit('Start Finals', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>
