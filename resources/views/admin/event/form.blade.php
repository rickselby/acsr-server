

<div class="form-group">
    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('start', 'Time', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        <div class='input-group date' id='datetimepicker1'>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            {{ Form::text('start', null, ['class' => 'form-control']) }}
        </div>
        <p class="help-block">
            Time in
            @if (\Auth::user() && \Auth::user()->timezone)
                {{ \Auth::user()->timezone }}
            @else
                UTC
            @endif
        </p>
        <script type="text/javascript">
            $(function () {
                $('#datetimepicker1').datetimepicker({
                    sideBySide: true,
                    format: "Do MMMM YYYY, HH:mm"
                });
            });
        </script>
    </div>
</div>

<div class="form-group">
    {!! Form::label('car_model', 'Car', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::text('car_model', null, ['class' => 'form-control']) !!}
        <p class="help-block">
            Car model - use the Assetto Corsa model identifier... e.g. <tt>ks_mazda_mx5_cup</tt>
        </p>
    </div>
</div>

<div class="form-group">
    {!! Form::label('automate', 'Automate?', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::checkbox('automate', 1) !!}
        <p class="help-block">
            Manually run the event, or allow the system to run it automatically?
        </p>
    </div>
</div>

<h2 class="col-sm-offset-2">Heats</h2>

<div class="form-group">
    {!! Form::label('laps_per_heat', 'Laps', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('laps_per_heat', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('points_sequence_id', 'Points Sequence', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::select('points_sequence_id', $pointsSequenceSelect, null, ['class' => 'form-control']) !!}
    </div>
</div>


<h2 class="col-sm-offset-2">Finals</h2>

<div class="form-group">
    {!! Form::label('drivers_per_final', 'Drivers qualifying', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('drivers_per_final', null, ['class' => 'form-control']) !!}
        <p class="help-block">
            The number of drivers that automatically qualify for a final
        </p>
    </div>
</div>

<div class="form-group">
    {!! Form::label('advance_per_final', 'Advance per final', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('advance_per_final', null, ['class' => 'form-control']) !!}
        <p class="help-block">
            The number of drivers that will advance from one final to the next
        </p>
    </div>
</div>

<div class="form-group">
    {!! Form::label('laps_per_final', 'Laps', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
        {!! Form::number('laps_per_final', null, ['class' => 'form-control']) !!}
    </div>
</div>
