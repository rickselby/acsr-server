<div class="col-sm-2">
    <strong>
        {{ ucfirst($provider) }}
    </strong>
</div>
<div class="col-sm-8">
    @if ($user->getProvider($provider))
        Connected
    @else
        Not connected
    @endif
</div>
@if ($user->getProvider($provider))
    <div class="col-sm-2">
        @if (count($user->providers) > 1)
            {{ \Form::open(['route' => ['auth.destroy', $provider], 'method' => 'delete', 'class' => 'form-inline']) }}
                <button class="btn btn-danger btn-xs">
                    Remove login
                </button>
            {{ \Form::close() }}
        @endif
    </div>
@endif
