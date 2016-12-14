@extends('page')

@section('header')
    <div class="page-header">
        <h1>Add a new event</h1>
    </div>
@endsection

@section('content')

    {!! Form::open(['route' => 'admin.event.store', 'class' => 'form-horizontal']) !!}

    @include('admin.event.form')

    <div class="form-group">
        <div class="col-sm-2"></div>
        <div class="col-sm-10">
            {!! Form::submit('Add Event', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection
