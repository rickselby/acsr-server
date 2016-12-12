@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Manage {{ $user->name }}
        </h1>
    </div>

@endsection

@section('content')

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