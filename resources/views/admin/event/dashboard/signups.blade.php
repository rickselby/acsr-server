
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a role="button" data-toggle="collapse" href="#signups">
                Event Signups
                <span class="badge">{{ $event->signups->count() }}</span>
            </a> <span class="caret"></span>
        </h3>
    </div>
    <div class="panel-collapse collapse {{ $event->races->count() ? '' : 'in' }}" id="signups" role="tabpanel">
        <div class="panel-body">

            Users must have both discord and steam linked to be able to race. Check the list below and let users
            know what they need to do to be eligible.

            Also, people don't always turn up, so you can remove them here.

        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>User</th>
                    @foreach(\AuthProviders::required() as $provider)
                        <th class="text-center">{{ ucfirst($provider) }}</th>
                    @endforeach
                    <th class="text-center">On Server?</th>
                    <th class="text-right">Remove</th>
                </tr>
            </thead>
            @foreach($event->signups AS $user)
                <tr>
                    <th class="{{ $user->hasRequiredProviders() ? 'success' : 'danger' }}">{{ $user->name }}</th>

                    @foreach(\AuthProviders::required() as $provider)
                        @if ($user->getProvider($provider))
                            <td class="success text-center">
                                <span class="fa fa-check" style="color: green"></span>
                            </td>
                        @else
                            <td class="danger text-center">
                                <span class="fa fa-times" style="color: red"></span>
                            </td>
                        @endif
                    @endforeach

                    @if ($user->on_server)
                        <td class="success text-center">
                            <span class="fa fa-check" style="color: green"></span>
                        </td>
                    @else
                        <td class="danger text-center">
                            <span class="fa fa-times" style="color: red"></span>
                        </td>
                    @endif

                    <td class="text-right">
                        {{ Form::open(['route' => ['admin.event.dashboard.signup.destroy', $event, $user], 'method' => 'delete']) }}
                            {{ Form::submit('Remove', ['class' => 'btn btn-danger btn-xs']) }}
                        {{ Form::close() }}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
