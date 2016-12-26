
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a role="button" data-toggle="collapse" href="#servers">
                Servers
                <span class="badge">{{ $event->servers->count() }}</span>
            </a> <span class="caret"></span>
        </h3>
    </div>
    <div class="panel-collapse collapse" id="servers" role="tabpanel">

        <table class="table table-hover">
            <thead>
            <tr>
                <th>Provider ID</th>
                <th>IP</th>
                <th>Root Password</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($servers AS $server)
                <tr>
                    <td>{{ $server['server']->provider_id }}</td>
                    <td>{{ $server['server']->ip }}</td>
                    <td>{{ $server['server']->password }}</td>
                    <td>
                        @if ($server['available'])
                            @if ($server['server']->race_id)
                                Running
                            @else
                                Ready
                            @endif
                        @else
                            Booting...?
                        @endif

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
