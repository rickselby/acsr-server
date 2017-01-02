
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            Run Next Session
        </h3>
    </div>
    <div class="panel-body">
        {!! Form::open(['route' => ['admin.event.dashboard.run-next-session', $event]]) !!}
            {!! Form::submit('Run Next Session', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>
