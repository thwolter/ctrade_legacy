@extends('layouts.master')

@section('content')

    <div class="content">
        <div class="container">
            
            <div class="row">

                <div class="col-md-3 col-sm-6">
                    @component('partials.icon-stat')
                        @slot('label', 'Wert')
                        @slot('value', $portfolio->present()->total())
                    @endcomponent
                </div> <!-- /.col-md-3 -->

                <div class="col-md-3 col-sm-6">
                    @component('partials.icon-stat')
                        @slot('label', 'Barbestand')
                        @slot('value', $portfolio->present()->cash())
                    @endcomponent
                </div> <!-- /.col-md-3 -->

                <div class="col-md-3 col-sm-6">
                    @component('partials.icon-stat')
                        @slot('label', 'Risiko (30 Tage)')
                        @slot('value', $portfolio->present()->total())
                    @endcomponent
                </div> <!-- /.col-md-3 -->

                <div class="col-md-3 col-sm-6">
                    @component('partials.icon-stat')
                        @slot('label', 'Entwicklung (30 Tage)')
                        @slot('value', $portfolio->present()->total())
                    @endcomponent
                </div> <!-- /.col-md-3 -->

               <div class="col-md-6">
                   <portlet title="Positionen">
                       <positions-chart></positions-chart>
                   </portlet>
               </div>
               

                <div class="col-md-6">
                    <portlet title="Wertentwicklung">
                         <graph></graph>
                    </portlet>
                </div>

                <div class="col-md-6">
                    <portlet title="Risikoentwicklung">
                        <p>a line chart</p>
                    </portlet>
                </div>
                
                 <div class="col-md-6">
                    <portlet title="Verteilung des Risikos">
                        <p>a donut chart</p>
                    </portlet>
                </div>

            </div> <!-- /.row -->

        </div> <!-- /.container -->
    </div> <!-- /.content -->



@endsection



@section('scripts.footer')

@endsection