@if (isset($errors) && count($errors) > 0)
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">There were errors</h3>
        </div>
        <ul class="list-group">
            @foreach ($errors->all() as $error)
                <li class="list-group-item">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
