@extends('page')

@section('header')
    <div class="page-header">
        <h1>Manage "{{ $role->name }}" Role</h1>
    </div>
@endsection

@section('content')

    {!! Form::open(['route' => ['admin.role.destroy', $role], 'method' => 'delete', 'class' => 'form-inline']) !!}
        <a class="btn btn-small btn-warning"
           href="{{ route('admin.role.edit', $role) }}">Edit Role</a>
        {!! Form::submit('Delete Role', array('class' => 'btn btn-danger')) !!}
    {!! Form::close() !!}

    <h2>Role permissions</h2>

    @if ($permissions->count())
        <h3>Add another permission</h3>
        {!! Form::open(['route' => ['admin.role.add-permission', $role], 'class' => 'form-horizontal']) !!}
        <div class="form-group">
            <div class="col-sm-4">
                {!! Form::select('permission', $permissions->pluck('name', 'id'), null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-sm-8">
                {!! Form::submit('Add permission to this role', array('class' => 'btn btn-success')) !!}
            </div>
        </div>
        {!! Form::close() !!}
    @endif

    <h3>Current Permissions</h3>

    <ul class="list-group">
        @foreach($role->permissions AS $permission)
            <li class="list-group-item container">
                <div class="col-sm-10">{{ $permission->name }}</div>
                <div class="col-sm-2">
                    {!! Form::open(['route' => ['admin.role.remove-permission', $role, $permission], 'method' => 'delete', 'class' => 'form-horizontal']) !!}
                        {!! Form::submit('Remove Permission', ['class' => 'btn btn-danger btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
            </li>
        @endforeach
    </ul>

    <h2>Users</h2>

    @if ($users->count())
        <h3>Add another user</h3>
        {!! Form::open(['route' => ['admin.role.add-user', $role], 'class' => 'form-horizontal']) !!}
        <div class="form-group">
            <div class="col-sm-4">
                {!! Form::select('user', $users->pluck('name', 'id'), null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-sm-8">
                {!! Form::submit('Add user to this role', array('class' => 'btn btn-success')) !!}
            </div>
        </div>
        {!! Form::close() !!}
    @endif

    <h3>Current Users</h3>

    <ul class="list-group">
        @foreach($role->users AS $user)
            <li class="list-group-item container">
                <div class="col-sm-10">{{ $user->name }}</div>
                <div class="col-sm-2">
                    {!! Form::open(['route' => ['admin.role.remove-user', $role, $user], 'method' => 'delete', 'class' => 'form-horizontal']) !!}
                        {!! Form::submit('Remove User', ['class' => 'btn btn-danger btn-xs']) !!}
                    {!! Form::close() !!}
                </div>
            </li>
        @endforeach
    </ul>

@endsection