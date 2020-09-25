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
    <div class="container-migration col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 p-0">

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
                                        @include('migrations._routes')
                                    @endforeach
                                    @php
                                        $groups = $company->routes->where('as_group', true);
                                    @endphp
                                    @if(count($groups))
                                        <hr class="hr">
                                        <h2>@lang('GROUPS'):</h2>
                                        @foreach($groups->sortBy('name') as $route)
                                            @include('migrations._routes')
                                        @endforeach
                                    @endif
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
                    <h1 class="text-uppercase">
                        <i class="fa fa-rocket"></i> @lang('Migration interface')
                    </h1>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <iframe class="container-migration col-md-12" src="" style="height: 450px;"></iframe>
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

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#modal-migration, #modal-upload-kmz, #modal-calibration').on('hidden.bs.modal', function () {
                //$('.container-migration').empty().hide().html($('#animated-loading').html()).show();
                //location.reload();
            });
        });
    </script>
@endsection
