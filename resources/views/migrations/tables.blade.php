@extends('layout')

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Configurations')</a></li>
        <li><a href="javascript:;">@lang('Migrations')</a></li>
        <li class="active">@lang('Tables')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Migrations')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Tables')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <!-- begin panel-group -->
            <div class="panel-group" id="accordion">
            @foreach($tables as $table)
                <!-- begin panel -->
                    <div class="panel panel-inverse overflow-hidden">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <a  class="accordion-toggle accordion-toggle-styled collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse-{{$loop->index}}" aria-expanded="false">
                                    <i class="fa fa-plus-circle pull-right"></i>
                                    @lang('Table') <b class="text-uppercase">{{ $table->name }}</b>
                                    @php
                                        $news = $table->total - $table->total_migrated;
                                    @endphp
                                    <button class="btn btn-xs pull-right btn-warning {{$news<=0?'hide':'active'}}">{{ $news }} @lang('News')</button>
                                </a>
                            </h3>
                        </div>
                        <div id="collapse-{{$loop->index}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body">
                                <div class="widget widget-stat bg-inverse-light text-white text-center">
                                    <div class="btn-group p-t-10">
                                        <button class="btn btn-sm btn-lg btn-lime" onclick="$('.name-route').text('{{ $table->name }}');$('.container-migration').attr('src','{{ $table->route }}')" data-toggle="modal" data-target="#modal-migration">
                                            <i class="fa fa-database"></i> @lang('Migrate')
                                        </button>
                                        <button type="button" class="btn btn-sm btn-lg btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="text-danger" onclick="$('.name-route').text('{{ $table->name }}. Deleting...');$('.container-migration').attr('src','{{ $table->route }}?delete=true')" data-toggle="modal" data-target="#modal-migration">
                                                    <i class="fa fa-exclamation-circle"></i> @lang('Delete all registers')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="widget-stat-number">{{ $table->total }} @lang('Registers')</div>
                                    <div class="widget-stat-number">{{ $table->total_migrated }} @lang('Migrated')</div>
                                </div>
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
