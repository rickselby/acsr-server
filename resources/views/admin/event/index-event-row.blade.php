<li class="list-group-item container-fluid">
    <div class="row">
        <div class="row-vertical">
            <div class="col-sm-3 col-vertical">
                <a href="{{ route('admin.event.show', $event) }}">
                    {{ $event->name }}
                </a>
            </div>
            <div class="col-sm-3 col-vertical">
                {{ \Times::toUserTimezone($event->start) }}
            </div>
            <div class="col-sm-3 col-vertical">
                {{ $event->signups->count() }} signups
            </div>
            <div class="col-sm-3 col-vertical">
                @if ($event->servers->count())
                    <a href="{{ route('admin.event.dashboard', $event) }}" class="btn btn-primary btn-block">
                        Dashboard
                    </a>
                @endif
            </div>
        </div>
    </div>
</li>
