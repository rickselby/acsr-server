@extends('page')

@section('header')
    <div class="page-header">
        <h1>{{ $sequence->name }} Points Sequence</h1>
    </div>
@endsection

@section('content')

    {!! Form::open(['route' => ['admin.points-sequence.destroy', $sequence], 'method' => 'delete', 'class' => 'form-inline']) !!}
    <a class="btn btn-small btn-warning"
       href="{{ route('admin.points-sequence.edit', $sequence) }}">Edit Sequence</a>
    {!! Form::submit('Delete Sequence', array('class' => 'btn btn-danger')) !!}
    {!! Form::close() !!}

    <div class="col-xs-2 col-xs-offset-5">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th>Position</th>
                <th>Points</th>
            </tr>
            </thead>
            <tbody>
                @foreach($sequence->points AS $point)
                <tr>
                    <th>{{ $point['position'] }}</th>
                    <td>{{ $point['points'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {!! Form::close() !!}

@endsection