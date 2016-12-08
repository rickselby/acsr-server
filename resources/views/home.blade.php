@extends('page')

@section('header')
    <div class="page-header">
        <h1>
            Assetto Corsa Sprint Racing
        </h1>
    </div>

@endsection

@section('content')

    <h2>What is sprint racing?</h2>

    <p>
        "Sprint" is used for many different types of motorsport - here we're using the
        <a href="https://en.wikipedia.org/wiki/Kart_racing#Sprint">karting definition</a>:
    </p>
    <blockquote>
        <p>
            The sprint format is a series of short-duration races, normally for a small
                number of laps, that qualify for a final, with a variety of point scoring
                calculations to determine the event's overall winner
            ...
            Here, speed and successful passing is of the most importance.
        </p>
    </blockquote>
    <p>
        The exact format will depend on the number of drivers involved. As an example:
    </p>
    <ul>
        <li>Each heat contains 8 drivers</li>
        <li>Each driver races 4 heats; once from each row of the grid (two from the left, two from the right)</li>
        <li>Points will be given for each heat, and combined to decide the final grids (12 drivers per final)</li>
        <li>The last final will be run first; the top two drivers from this final will move forward to the next final</li>
    </ul>
    <p>
        This is all still in early stage planning, and details may change.
    </p>

@endsection