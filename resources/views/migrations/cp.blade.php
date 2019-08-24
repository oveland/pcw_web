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
                            @foreach($company->routes->where('as_group', false)->sortBy('name') as $route)
                                <!-- begin panel -->
                                    <div class="panel panel-inverse overflow-hidden">
                                        <div class="panel-heading">
                                            <h3 class="panel-title" aria-expanded="false">
                                                <div class="accordion-toggle accordion-toggle-styled collapsed row">
                                                    <div class="col-md-4">
                                                        <i class="fa fa-road"></i>
                                                        <a data-toggle="collapse" data-parent="#accordion-{{$company->id}}" href="#collapse-{{$route->id}}" class="text-white">
                                                            ({{$route->id}}) <b class="text-uppercase">{{ $route->name }}</b>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <samp class="{{ $route->controlPoints->count() ? 'text-white text-bold' : 'text-muted' }}">
                                                            {{ $route->controlPoints->count() }} @lang('Control Points')
                                                        </samp>
                                                    </div>
                                                </div>
                                            </h3>
                                        </div>
                                        <div id="collapse-{{ $route->id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                            <div class="panel-body p-t-0">
                                                <div class="row widget widget-stat bg-inverse-light text-white p-10 text-center">
                                                    <div class="btn-group btn-group-circle btn-group-solid">
                                                        <a href="#modal-upload-kmz" onclick="$('#kmz-route-id').val({{ $route->id }});$('#kmz-route-name-id').text('{{ $route->id."_" }}')" class="btn btn-warning" data-toggle="modal">
                                                            <i class="fa fa-upload"></i> @lang('Upload KMZ')
                                                        </a>
                                                        <a href="{{ $route->url }}" class="btn btn-info tooltips" title="{{ $route->url }}" data-placement="bottom" data-html="true">
                                                            <i class="fa fa-download"></i> @lang('Download KMZ')
                                                        </a>
                                                        <a href="{{ route('export-coordinates',['route'=>$route->id]) }}" class="btn btn-primary">
                                                            <i class="fa fa-map-marker"></i> @lang('Export KMZ Coordinates')
                                                        </a>
                                                        <a href="#modal-migration" class="btn btn-success" data-toggle="modal">
                                                            <i class="fa fa-database"></i> @lang('Process migration')
                                                        </a>

                                                        <div class="btn-group btn-group-solid">
                                                            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa fa-cogs"></i> @lang('Calibrate route')
                                                                <i class="fa fa-angle-down"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="#modal-calibration" class="text-primary" data-toggle="modal"
                                                                       onclick="$('.route-name').html('{{ $route->name }}');$('.response-calibration').empty().load('{{ route('migrate-cp-calibrate-route', ['route' => $route->id, 'apply' => "false"]) }}')">
                                                                        <i class="fa fa-cog"></i> @lang('Calibrate and check')
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#modal-calibration" class="text-danger" data-toggle="modal"
                                                                       onclick="$('.route-name').html('{{ $route->name }}');$('.response-calibration').empty().load('{{ route('migrate-cp-calibrate-route', ['route' => $route->id, 'apply' => "true"]) }}')">
                                                                        <i class="fa fa-cog fa-spin"></i> @lang('Calibrate and save')
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 p-t-10">
                                                        <div class="row tabbable-custom nav-justified">
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
                            <iframe class="container-migration col-md-12" src="{{ route('migrate') }}" style="height: 800px"></iframe>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-calibration">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">@lang('Calibration for Control Points'): <b class="route-name text-uppercase"></b></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 pre">
                            <pre class="response-calibration"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-upload-kmz">
        <form id="form-create-sim-gps" action="{{ route('migrate-cp-upload-kmz') }}" method="POST" class="form-create-sim-gps" enctype="multipart/form-data">
            <input type="hidden" name="company" value="{{ $companyRequest }}">
            <input type="hidden" name="route" id="kmz-route-id" value="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">
                            <i class="fa fa-upload"></i> @lang('Upload KMZ')
                        </h4>
                    </div>
                    <div class="modal-body">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="form-group">
                                    <div class="input-group input-icon right">
                                        <span class="input-group-addon">
                                            <i class="fa fa-file-zip-o font-blue-chambray"></i> @lang('File')
                                        </span>
                                        <input type="file" name="kmz" id="kmz-file" class="form-control" required="true">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group input-icon right">
                                        <span class="input-group-addon">
                                            <i class="fa fa-tag"></i> @lang('File name'): <span id="kmz-route-name-id"></span>
                                        </span>
                                        <input type="text" size="50" name="name" id="kmz-name" class="form-control input-sm" placeholder="@lang('Name without spaces')" required="true">
                                        <span class="input-group-addon">
                                            .kmz
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn width-100 btn-default btn-rounded btn-sm" data-dismiss="modal">
                            <i class="fa fa-undo"></i> @lang('Close')
                        </button>
                        <button type="submit" class="btn width-100 btn-lime btn-rounded btn-sm">
                            <i class="fa fa-cloud-upload"></i> @lang('Upload')
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
