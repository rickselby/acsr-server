@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            {{ $event->name }}
        </h1>
    </div>
@endsection

@section('content')

    <h2>Are you sure you wish to delete {{ $event->name }}?</h2>
    {!! Form::open(['route' => ['admin.event.destroy', $event], 'method' => 'delete', 'class' => 'form-inline']) !!}
        {!! Form::submit('Yes, delete it', array('class' => 'btn btn-danger')) !!}
        <a class="btn btn-small btn-primary"
           href="{{ route('admin.event.show', $event) }}">No, keep it</a>
    {!! Form::close() !!}


@endsection