@extends('page')

@section('header')
    <div class="page-header">
        <h1>Update Points Sequence</h1>
    </div>
@endsection

@section('content')

    {!! Form::model($sequence, ['route' => ['admin.points-sequence.update', $sequence], 'method' => 'put', 'class' => 'form-horizontal']) !!}

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
            @for($i = 1; $i <= max(count($sequence->points), 49) + 1; $i++)
                <tr>
                    <th>{{ $i }}</th>
                    <td>
                        <input type="number" name="points[{{ $i }}]"
                               value="{{ $sequence->points->where('position', $i)->first()->points or '' }}" />
                    </td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <div class="col-sm-2"></div>
        <div class="col-sm-10">
            {!! Form::submit('Update Points Sequence', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>

    {!! Form::close() !!}

@endsection
