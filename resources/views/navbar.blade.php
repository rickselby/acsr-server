<!-- Static navbar -->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">
                ACSR
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <!--
                <li>
                    <a href="#">Something?</a>
                </li>
                -->

                <!-- Admin options -->
                @if (Gate::check('role-admin') || Gate::check('user-admin'))
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Administration <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @can('role-admin')
                                <li>
                                    <a href="{{ route('admin.role.index') }}">Role Management</a>
                                </li>
                            @endcan
                            @can('user-admin')
                                <li>
                                    <a href="{{ route('admin.user.index') }}">User Management</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
            </ul>

            <!-- User login / management -->
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::check())
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            {{ \Auth::user()->name }} <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('user.logins')}}">Manage Logins</a>
                            </li>
                            <li>
                                <a href="{{route('auth.logout')}}">Logout</a>
                            </li>
                        </ul>
                    </li>
                @else

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Login <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">

                            @foreach(\AuthProviders::all() AS $provider)
                            <li>
                                <a href="{{ route('auth', $provider) }}">
                                    @include('login-buttons.'.$provider, ['class' => 'btn-block'])
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </li>

                @endif
            </ul>
        </div><!--/.nav-collapse -->
    </div><!--/.container-fluid -->
</nav>