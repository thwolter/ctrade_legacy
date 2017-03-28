@extends('layouts.master')

@section('content')

    <div class="panel panel-default">
        <div class="panel-heading">Portfolio erstellen</div>

        <div class="panel-body">

            <form method="post" action="/portfolios/{{ $portfolio->id }}">

                <input type="hidden" name="_method" value="PUT">

                {{ csrf_field() }}

                @include('layouts.errors')

                <div class="input-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $portfolio->name }}">
                </div>
                <br>

                <div class="input-group">
                    <label for="currency">Währung</label>
                    <input type="text" name="currency" class="form-control" value="{{ $portfolio->currency }}">
                </div>
                <br>

                <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary">Ändern</button>
                </div>

            </form>

        </div>
    </div>

@endsection



