@extends('layouts.master')

@section('content')

    <div class="content">
        <div class="container">

            <div class="page-header">
                <h3 class="page-title">Einstellungen</h3>

               {{-- <ol class="breadcrumb">
                    <li><a href="./">Dashboard</a></li>
                    <li><a href="#">Demo Pages</a></li>
                    <li class="active">Settings</li>
                </ol>--}}
            </div> <!-- /.page-header -->

            <div class="portlet portlet-boxed">
                <div class="portlet-body">
                    <div class="layout layout-main-right layout-stack-sm">

                        <div class="col-md-3 col-sm-4 layout-sidebar">

                            <div class="nav-layout-sidebar-skip">
                                <br class="xs-20 visible-xs"/>
                                <strong>Tab Navigation</strong> / <a href="#settings-content">Skip to Content</a>
                            </div>

                            @php
                                if (!session('active')) session(['active' => 'portfolio'])
                            @endphp

                            <ul id="myTab" class="nav nav-layout-sidebar nav-stacked">

                                <li class="{{ active_tab(session('active'), 'portfolio') }}">
                                    <a href="#portfolio" data-toggle="tab">
                                        <i class="fa fa-lock"></i>
                                        &nbsp;&nbsp;Portfolio
                                    </a>
                                </li>

                                <li class="{{ active_tab(session('active'), 'limits') }}">
                                    <a href="#limits" data-toggle="tab">
                                        <i class="fa fa-signal"></i>
                                        &nbsp;&nbsp;Limite
                                    </a>
                                </li>

                                <li class="{{ active_tab(session('active'), 'notification') }}">
                                    <a href="#notification" data-toggle="tab">
                                        <i class="fa fa-bullhorn"></i>
                                        &nbsp;&nbsp;Benachrichtigungen
                                    </a>
                                </li>

                            </ul>

                        </div> <!-- /.col -->


                        <div class="col-md-9 col-sm-8 layout-main">

                            <div id="settings-content" class="tab-content stacked-content">

                                @include('portfolios.edit.portfolio')

                                @include('portfolios.edit.limits')

                                @include('portfolios.edit.notification')

                            </div> <!-- /.tab-content -->

                        </div> <!-- /.col -->
                    </div> <!-- /.row -->
                </div> <!-- /.portlet-body -->
            </div> <!-- /.portlet -->
        </div> <!-- /.container -->
    </div> <!-- .content -->

@endsection
