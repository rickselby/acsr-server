@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Your settings
        </h1>
    </div>

@endsection

@section('content')

    {!! Form::open(['route' => 'user.update-settings', 'class' => 'form-horizontal']) !!}
        <div class="form-group">
            {!! Form::label('timezone', 'Timezone', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-10">
                {!! Timezone::selectForm($user->timezone ?: 'UTC', null, array('class' => 'form-control', 'name' => 'timezone')) !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                {!! Form::submit('Update Settings', array('class' => 'btn btn-success')) !!}
            </div>
        </div>
    {!! Form::close() !!}


@endsection