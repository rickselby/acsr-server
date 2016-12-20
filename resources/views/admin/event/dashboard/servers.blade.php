
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
            @foreach($event->servers AS $server)
                <tr>
                    <td>{{ $server->provider_id }}</td>
                    <td>{{ $server->ip }}</td>
                    <td>{{ $server->password }}</td>
                    <td>?</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
