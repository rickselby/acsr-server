@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Manage {{ $user->name }}
        </h1>
    </div>

@endsection

@section('content')

    <h2>Update Details</h2>

    {!! Form::model($user, ['route' => ['admin.user.update', $user], 'method' => 'put', 'class' => 'form-horizontal']) !!}

    <div class="form-group">
        {!! Form::label('number', 'Number', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('number', null, ['class' => 'form-control']) !!}
            <p class="help-block">
                Text field - can be '001' or whatever
            </p>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-10 col-sm-offset-2">
            {!! Form::submit('Update Details', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

    <h2>Authorized logins</h2>

    <ul class="list-group">
        @foreach(\AuthProviders::required() AS $provider)
            <li class="list-group-item container {{ $user->getProvider($provider) ? 'list-group-item-success' : 'list-group-item-danger' }}">
                @include('admin.user.provider-row')
            </li>
        @endforeach
            @foreach(\AuthProviders::optional() AS $provider)
                <li class="list-group-item container {{ $user->getProvider($provider) ? 'list-group-item-success' : '' }}">
                    @include('admin.user.provider-row')
                </li>
            @endforeach
    </ul>

    <h2>Delete User</h2>

    {!! Form::open(['route' => ['admin.user.destroy', $user], 'method' => 'delete']) !!}
        {!! Form::submit('Delete user', array('class' => 'btn btn-danger')) !!}
    {!! Form::close() !!}

@endsection