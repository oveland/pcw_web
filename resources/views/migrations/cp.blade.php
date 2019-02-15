@extends('layout')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Configurations')</a></li>
        <li><a href="javascript:;">@lang('Migrations')</a></li>
        <li class="active">@lang('Control Points')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Migrations')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Control Points')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="col-md-12 p-0">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    @include('layouts.panels.expand')
                </div>
                <div class="row">
                    <div class="col-md-11">
                        <ul class="nav nav-tabs nav-tabs-info nav-justified">
                            @foreach($companies as $company)
                                <li class="{{$loop->first?'active':''}}">
                                    <a href="#company-tab-{{$loop->index}}" data-toggle="tab" aria-expanded="true" class="text-uppercase">
                                        ({{$company->id}})
                                        <br>
                                        {{$company->short_name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="tab-content panel p-0">
                @foreach($companies as $company)
                    <div id="company-tab-{{$loop->index}}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}} p-10">
                        <div class="col-md-12">
                            <!-- begin panel-group -->
                            <div class="panel-group" id="accordion-{{$company->id}}">
                            @if($company->routes)
                            @foreach($company->routes->sortBy('name') as $route)
                                <!-- begin panel -->
                                    <div class="panel panel-inverse overflow-hidden">
                                        <div class="panel-heading">
                                            <h3 class="panel-title" aria-expanded="false">
                                                <span class="accordion-toggle accordion-toggle-styled collapsed">
                                                    <i class="fa fa-road"></i>
                                                    <a data-toggle="collapse" data-parent="#accordion-{{$company->id}}" href="#collapse-{{$route->id}}" class="text-white">
                                                        ({{$route->id}}) <b class="text-uppercase">{{ $route->name }}</b>
                                                    </a>
                                                    <i class="fa fa-plus-circle pull-right"></i>
                                                    <a href="{{ $route->url }}" class="pull-right m-r-20 text-white">
                                                        <i class="fa fa-download"></i> @lang('Download KMZ')
                                                    </a>
                                                    <a href="{{ route('export-coordinates',['route'=>$route->id]) }}" class="pull-right m-r-20 text-white">
                                                        <i class="fa fa-map-marker"></i> @lang('Export Coordinates')
                                                    </a>
                                                </span>
                                            </h3>
                                        </div>
                                        <div id="collapse-{{ $route->id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                            <div class="panel-body">
                                                <div class="widget widget-stat bg-inverse-light text-white p-b-30">
                                                    <div class="widget-stat-btn"><a href="#" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                                                    <button class="btn btn-success" onclick="alert('Migrating...')" data-toggle="modal" data-target="#modal-migration">
                                                        <i class="fa fa-database"></i> @lang('Process migration')
                                                    </button>
                                                    <button class="btn btn-default" onclick="$('.compare-{{$route->id}}').load('{{ route('migrate-cp-compare',['route'=>$route->id]) }}')">
                                                        <i class="fa fa-angle-double-left"></i> <i class="fa fa-angle-double-right"></i> @lang('Compare')
                                                    </button>
                                                    <div class="widget-stat-number">
                                                        {{ $route->controlPoints->count() }} @lang('Control Points')
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="panel">
                                                            <ul class="nav nav-tabs nav-tabs-primary nav-justified">
                                                                @foreach(\App\Models\Routes\DayType::all()->sortBy('id') as $dayType)
                                                                    <li class="{{ $loop->first ? 'active': '' }}">
                                                                        <a href=".day-type-{{ $dayType->id }}-{{ $route->id }}" data-toggle="tab">
                                                                            <i class="fa fa-calendar-check-o text-info" aria-hidden="true"></i>
                                                                            {{ $dayType->description }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    @if($route->controlPoints)
                                                        @include('migrations._controlPoints')
                                                    @endif
                                                    <div class="compare-{{$route->id}} text-left"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end panel -->
                                @endforeach
                                @else
                                    @lang('Without routes!')
                                @endif
                            </div>
                            <!-- end panel-group -->
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-migration">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">@lang('Process migration for Control Points'): <b class="route-name text-uppercase"></b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <iframe class="container-migration col-md-12" style="height: 450px"></iframe>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>
@endsection
