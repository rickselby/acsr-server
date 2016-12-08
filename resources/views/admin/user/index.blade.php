@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            User Management
        </h1>
    </div>
@endsection

@section('content')

    <table class="table table-striped table-hover table-condensed">
        <tr>
            <th>User</th>
            <th>New?</th>
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
                <td>{{ $user->new ? 'New' : '' }}</td>

                @foreach(\AuthProviders::required() as $provider)
                    @if (\Auth::user()->getProvider($provider))
                    <td class="success text-center">
                        <span class="fa fa-check" style="color: green"></span>
                    @else
                    <td class="danger text-center">
                        <span class="fa fa-times" style="color: red"></span>
                    @endif
                    </td>
                @endforeach
                @foreach(\AuthProviders::optional() as $provider)
                    @if (\Auth::user()->getProvider($provider))
                    <td class="success text-center">
                        <span class="fa fa-check" style="color: green"></span>
                    @else
                    <td class="text-center">
                        <span class="fa fa-times"></span>
                    @endif
                    </td>
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