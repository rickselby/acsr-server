@extends('page')

@section('header')
    <div class="page-header">
        <h1>Add a new points system</h1>
    </div>
@endsection

@section('content')

    {!! Form::open(['route' => 'admin.points-sequence.store', 'class' => 'form-horizontal']) !!}

    <div class="form-group">
        {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
        <div class="col-sm-10">
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-xs-4 col-sm-offset-2">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th>Position</th>
                <th>Points</th>
            </tr>
            </thead>
            <tbody>
            @for($i = 1; $i <= 50; $i++)
                <tr>
                    <th>{{ $i }}</th>
                    <td>
                        <input type="number" name="points[{{ $i }}]" />
                    </td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <div class="col-sm-2"></div>
        <div class="col-sm-10">
            {!! Form::submit('Add Points System', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection
