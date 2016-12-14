@extends('page')


@section('header')
    <div class="page-header">
        <h1>Update event</h1>
    </div>
@endsection

@section('content')

    {!! Form::model($event, ['route' => ['admin.event.update', $event], 'method' => 'put', 'class' => 'form-horizontal']) !!}

    @include('admin.event.form')

    <div class="form-group">
        <div class="col-sm-2"></div>
        <div class="col-sm-10">
            {!! Form::submit('Update Event', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

    <div class="pull-right">
        <a class="btn btn-small btn-danger"
           href="{{ route('admin.event.verify-destroy', $event) }}">Delete Event</a>
    </div>

@endsection
