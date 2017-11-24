@extends('layouts.master')

@section('content')

    <!-- Header -->
    <section class="g-color-white g-bg-darkgray-radialgradient-circle g-pa-40">
        <div class="container">
            <div class="row">
                <div class="col-md-8 align-self-center">
                    <h2 class="h3 text-uppercase g-font-weight-300 g-mb-20 g-mb-0--md">
                        <strong>{{ $portfolio->name }}</strong>
                        Portfolio</h2>
                </div>
            </div>
        </div>
    </section>

    <div class="container g-pt-100 g-pb-20">
        <div class="row justify-content-between">

            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Main section -->
            <div class="col-lg-9 order-lg-2 g-mb-80">

                <!-- Summary Card -->
                @component('layouts.components.section', ['collapse' => false])

                    @slot('title')
                        {{ $stock->name }}
                    @endslot

                    @slot('subtitle')
                        ISIN: {{ $stock->isin }}
                    @endslot

                        <!-- Key Figures -->
                        <div class="col-md-10">
                            <div class="g-mb-10">
                                <div class="row">
                                    <a class="col-md-4 btn" data-toggle="popover" data-placement="bottom"
                                       data-title="Kurs"
                                       data-content="Schlusskurs der Börse Stuttgart vom 10.02.2017">
                                        <div class="font-weight-bold g-bg-black-opacity-0_8 g-color-white g-font-size-12 text-center text-uppercase g-pa-3">
                                            Kurs
                                        </div>
                                        <div class="d-flex g-bg-white g-height-70 justify-content-center g-bg-bluegray-opacity-0_1">
                                            <p class="align-self-center g-color-gray-dark-v3 g-font-size-20 g-font-weight-200">
                                                {{ $stock->present()->price() }}
                                            </p>
                                        </div>
                                    </a>
                                    <a class="col-md-4 btn" data-toggle="popover" data-placement="top"
                                       data-title="Rendite"
                                       data-content="Gewinn/Verlust über einen Zeitraum von 52 Wochen. Der Zeitraum kann in den <a href='#'>Einstellungen</a> festgelegt werden.">
                                        <div class="font-weight-bold g-bg-blue g-color-white g-font-size-12 text-center text-uppercase g-pa-3">
                                            Rendite
                                        </div>
                                        <div class="d-flex g-bg-white g-height-70 justify-content-center g-bg-bluegray-opacity-0_1">
                                            <p class="align-self-center g-color-gray-dark-v3 g-font-size-20 g-font-weight-200">
                                                {{ $stock->present()->price() }}
                                            </p>
                                        </div>
                                    </a>
                                    <a class="col-md-4 btn" data-toggle="popover" data-placement="bottom"
                                       data-title="Risiko"
                                       data-content="Das Risiko der Aktie für einen Zeitraum von 1 Monat mit einer Sicherheit von 95%. Zeitraum und Sicherheitslevel können über die <a href='#'>Einstellungen</a> festgelegt werden.">
                                        <div class="font-weight-bold g-bg-red g-color-white g-font-size-12 text-center text-uppercase g-pa-3">
                                            Risiko
                                        </div>
                                        <div class="d-flex g-bg-white g-height-70 justify-content-center g-bg-bluegray-opacity-0_1">
                                            <p class="align-self-center g-color-gray-dark-v3 g-font-size-20 g-font-weight-200">
                                                {{ $stock->present()->price() }}
                                            </p>
                                        </div>
                                    </a>
                                </div>

                            </div>

                        </div>

                @endcomponent

                <!-- Transaction Form -->
                @component('layouts.components.section')

                    @slot('title')
                        Neue Transaktion
                    @endslot

                    <stock-trade
                            :portfolio="{{ json_encode($portfolio) }}"
                            :instrument="{{ json_encode($stock) }}"
                            :prices="{{ json_encode($prices) }}"
                            store="{{ route('positions.store', [], false) }}"
                            redirect="#">
                    </stock-trade>

                @endcomponent

                <!-- History Chart -->
                @component('layouts.components.section')

                    @slot('title')
                        Wertentwicklung
                    @endslot

                    @slot('menu')
                        <a class="dropdown-item g-px-10" href="#">
                            <i class="icon-layers g-font-size-12 g-color-gray-dark-v5 g-mr-5"></i> Projects
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item g-px-10" href="#">
                            <i class="icon-plus g-font-size-12 g-color-gray-dark-v5 g-mr-5"></i> View More
                        </a>
                    @endslot

                    <stock-chart
                            :exchanges="{{ json_encode($exchanges) }}"
                            :history="{{ json_encode($history) }}">
                    </stock-chart>

                @endcomponent

                <!-- Performance Table -->
                @component('layouts.components.section')

                    @slot('title')
                        Table
                    @endslot

                    @slot('menu')
                        <a class="dropdown-item g-px-10" href="#">
                            <i class="icon-layers g-font-size-12 g-color-gray-dark-v5 g-mr-5"></i> Projects
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item g-px-10" href="#">
                            <i class="icon-plus g-font-size-12 g-color-gray-dark-v5 g-mr-5"></i> View More
                        </a>
                    @endslot

                    <stock-performance
                            :exchanges="{{ json_encode($exchanges) }}"
                            :history="{{ json_encode($history) }}"
                            :stock="{{ json_encode($stock) }}"
                            locale="de-DE">
                    </stock-performance>

                @endcomponent

            </div>
            <!-- End Accordion -->
        </div>
    </div>


@endsection



@section('link.header')

    <link rel="stylesheet" href="{{ asset('assets/vendor/custombox/custombox.min.css') }}">

@endsection



@section('script.footer')

    <!-- JS Unify -->
    <script src="{{ asset('assets/js/components/hs.modal-window.js') }}"></script>

    <!-- JS Implementing Plugins -->
    <script src="{{ asset('assets/vendor/custombox/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/popper.min.js') }}"></script>

    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function () {

            // initialization of popups
            $.HSCore.components.HSModalWindow.init('[data-modal-target]');

            // initialization of popovers
            $('[data-toggle="popover"]').popover({html: true});
        });
    </script>

@endsection

