<div class="col-sm-2">
    <strong>
        {{ ucfirst($provider) }}
    </strong>
</div>
<div class="col-sm-6">
    @if (\Auth::user()->getProvider($provider))
        <img src="{{ \Auth::user()->getProvider($provider)->avatar }}" style="height: 2em;" />
        {{ \Auth::user()->getProvider($provider)->name }}
    @else
        {{ \Form::open(['route' => ['auth', $provider], 'method' => 'get', 'class' => 'form-inline']) }}
            @include('login-buttons.'.$provider)
        {{ \Form::close() }}
    @endif
</div>
@if (\Auth::user()->getProvider($provider))
    <div class="col-sm-2">
        {{ \Form::open(['route' => ['auth', $provider], 'method' => 'get', 'class' => 'form-inline']) }}
        <button class="btn btn-info">
            Update Details
        </button>
        {{ \Form::close() }}
    </div>
    <div class="col-sm-2">
        @if (count(\Auth::user()->providers) > 1)
            {{ \Form::open(['route' => ['auth.destroy', $provider], 'method' => 'delete', 'class' => 'form-inline']) }}
                <button class="btn btn-danger">
                    Remove login
                </button>
            {{ \Form::close() }}
        @endif
    </div>
@endif
