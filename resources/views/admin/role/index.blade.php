@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Role Management
        </h1>
    </div>
@endsection

@section('content')

    <p>
        <a class="btn btn-small btn-info" href="{{ route('admin.role.create') }}">Add a new role</a>
    </p>

    <h2>Existing Roles</h2>
    <div class="list-group">
        @foreach($roles as $role)
            <a href="{{ route('admin.role.show', $role) }}" class="list-group-item">
                {{ $role->name }}
            </a>
        @endforeach
    </div>

@endsection