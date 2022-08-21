@extends('layouts.blank')

@section('stylesheets')
    <style>
        body{
            background-color: transparent !important;
        }
        .page-footer{
            text-align: center !important;
        }
        .page-footer-inner{
            float: none !important;
        }
    </style>
@endsection

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Configurations')</a></li>
        <li><a href="javascript:;">@lang('Migrations')</a></li>
        <li class="active">@lang('Tables')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i>
        @if($route)
            @php
                $routeModel = \App\Models\Routes\Route::find($route)
            @endphp
            <strong>{{ $routeModel->name }}</strong>
        @else
            <strong>@lang('All routes')</strong>
        @endif
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Migrations')</small>
    </h1>

    <!-- end page-header -->

    <div class="container container-migration">
        <!-- begin row -->
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <!-- begin panel-group -->
                <div class="panel-group" id="accordion">
                @foreach($tables as $table)
                    <!-- begin panel -->
                        <div class="panel panel-default overflow-hidden">
                            <div class="panel-heading row">
                                <div class="panel-title col-md-12">
                                    <span class="pull-left">
                                        <i class="fa fa-database"></i> @lang('Table') <b class="text-uppercase">{{ $table->name }}</b>
                                        <br>
                                        <span>{{ $table->total }} @lang('Registers') / {{ $table->total_migrated }} @lang('Migrated')</span>
                                    </span>
                                    @php($news = $table->total - $table->total_migrated)
                                    <span class="pull-right">
                                        <button class="btn btn-sm disabled btn-warning btn-circle {{$news <=0 ? 'hide':'active'}}">{{ $news }} @lang('News')</button>
                                        <button class="btn btn-sm btn-circle disabled btn-danger {{$news < 0 ? 'active':'hide'}}">
                                            <i class="fa fa-exclamation-triangle"></i> {{ abs($news) }} @lang('overloaded')
                                        </button>
                                        <button class="btn btn-sm btn-info btn-circle" onclick="$('.name-route').text('{{ $table->name }}');$('.container-migration').attr('src','{{ "$table->route?route=$route" }}')" data-toggle="modal" data-target="#modal-migration">
                                            <i class="fa fa-cogs"></i> @lang('Migrate')
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- end panel -->
                    @endforeach
                </div>
                <!-- end panel-group -->
            </div>
        </div>

        <div class="modal fade" id="modal-migration">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">@lang('Process migration for table ')<b class="name-route text-uppercase"></b></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <iframe class="container-migration col-md-12" src="" style="height: 450px"></iframe>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#modal-migration').on('hidden.bs.modal', function () {
                $('.container-migration').empty().hide().html($('#animated-loading').html()).show();
                location.reload();
            });
        });
    </script>
@endsection
