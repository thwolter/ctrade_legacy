@extends('layouts.portfolio')

@section('container-content')

    <div class="panel-body">
        @php ($item = $position->positionable)
        @include('positions/partials/stock.summary')

        <div class="space-70"></div>
        <div class="container">

            <h4>Aktien kaufen</h4>
            <div class="space-40"></div>

        {!! Form::open(['route' => ['positions.update', $portfolio->id, $position->id],
            'method' => 'PUT', 'class' => 'form-horizontal']) !!}

            {!! Form::hidden('direction', 'buy') !!}
            {!! Form::hidden('itemId', $item->id) !!}
            {!! Form::hidden('itemType', get_class($item)) !!}

            @include('positions.partials.transaction')

            <!-- Button (Double) -->
            <div class="space-70"></div>
            <div class="col-md-8 offset-md-3">
                {!! Form::submit('Kaufen', ['class' => 'btn theme-btn-color']) !!}
                <a href="{{ URL::previous() }}" class="btn btn-secondary">Abbrechen</a>
            </div>

            {!! Form::close() !!}
        </div>
    </div>

@endsection