@extends('layouts.master')

@section('content')

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="page-header">
        <h3 class="page-title">Portfolio Einstellungen</h3>
    </div> <!-- /.page-header -->

    <div class="portlet portlet-boxed">
        <div class="portlet-body">
            <div class="layout layout-main-right layout-stack-sm">

                <div class="col-md-3 col-sm-4 layout-sidebar">

                    <div class="nav-layout-sidebar-skip">
                        <br class="xs-20 visible-xs"/>
                        <strong>Tab Navigation</strong> / <a href="#settings-content">Skip to Content</a>
                    </div>

                    <ul id="myTab" class="nav nav-layout-sidebar nav-stacked">

                        <li role="presentation" class="{{ active_tab('portfolio') }}">
                            <a href="#portfolio" data-toggle="tab" role="tab">
                                <i class="fa fa-pie-chart"></i>
                                &nbsp;&nbsp;Portfolio
                            </a>
                        </li>

                        <li role="presentation" class="{{ active_tab('parameter') }}">
                            <a href="#parameter" data-toggle="tab" role="tab">
                                <i class="fa fa-calculator"></i>
                                &nbsp;&nbsp;Parameter
                            </a>
                        </li>

                        <li role="presentation" class="{{ active_tab('limits') }}">
                            <a href="#limits" data-toggle="tab" role="tab">
                                <i class="fa fa-bar-chart"></i>
                                &nbsp;&nbsp;Limite
                            </a>
                        </li>

                        <li role="presentation" class="{{ active_tab('dashboard') }}">
                            <a href="#dashboard" data-toggle="tab" role="tab">
                                <i class="fa fa-tachometer"></i>
                                &nbsp;&nbsp;Dashboard
                            </a>
                        </li>

                        <li role="presentation" class="{{ active_tab('notifications') }}">
                            <a href="#notifications" data-toggle="tab" role="tab">
                                <i class="fa fa-envelope"></i>
                                &nbsp;&nbsp;Benachrichtigungen
                            </a>
                        </li>

                    </ul>

                </div> <!-- /.col -->


                <div class="col-md-9 col-sm-8 layout-main">

                    <div id="settings-content" class="tab-content stacked-content">

                        @include('portfolios.edit.portfolio')

                        @include('portfolios.edit.parameter')

                        @include('portfolios.edit.limits')

                        @include('portfolios.edit.dashboard')

                        @include('portfolios.edit.notifications')

                    </div> <!-- /.tab-content -->

                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.portlet-body -->
    </div> <!-- /.portlet -->

@endsection
