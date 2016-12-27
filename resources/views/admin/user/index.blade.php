@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            User Management
        </h1>
    </div>
@endsection

@section('content')

    <p>
        <a href="{{ route('admin.user.refresh-names') }}">
            <button class="btn btn-info">
                Update Names and Server Status
            </button>
        </a>
    </p>

    <table class="table table-striped table-hover table-condensed">
        <tr>
            <th>User</th>
            <th></th>
            <th>New?</th>
            <th class="text-center">On Server?</th>
            @foreach(\AuthProviders::required() as $provider)
                <th class="text-center">{{ ucfirst($provider) }}</th>
            @endforeach
            @foreach(\AuthProviders::optional() as $provider)
                <th class="text-center">{{ ucfirst($provider) }}</th>
            @endforeach
            <th>Edit</th>
        </tr>
        @foreach($users AS $user)
            <tr>
                <th>{{ $user->name }}</th>
                <th>
                    <span class="badge driver-number">
                        {{ $user->number ?: '???' }}
                    </span>
                </th>

                @if ($user->new)
                    <td class="info text-center">New</td>
                @else
                    <td></td>
                @endif

                @if ($user->on_server)
                    <td class="success text-center">
                        <span class="fa fa-check" style="color: green"></span>
                    </td>
                @else
                    <td class="danger text-center">
                        <span class="fa fa-times" style="color: red"></span>
                    </td>
                @endif

                @foreach(\AuthProviders::required() as $provider)
                    @if ($user->getProvider($provider))
                        <td class="success text-center">
                            <span class="fa fa-check" style="color: green"></span>
                            @if ($user->new)
                                <em>({{ $user->getProvider($provider)->name }})</em>
                            @endif
                        </td>
                    @else
                        <td class="danger text-center">
                            <span class="fa fa-times" style="color: red"></span>
                        </td>
                    @endif
                @endforeach

                @foreach(\AuthProviders::optional() as $provider)
                    @if ($user->getProvider($provider))
                        <td class="success text-center">
                            <span class="fa fa-check" style="color: green"></span>
                        </td>
                    @else
                        <td class="text-center">
                            <span class="fa fa-times"></span>
                        </td>
                    @endif
                @endforeach

                <th>
                    <a href="{{ route('admin.user.edit', $user) }}">
                        <button class="btn btn-warning btn-xs">
                            Edit
                        </button>
                    </a>
                </th>
            </tr>
        @endforeach
    </table>

@endsection