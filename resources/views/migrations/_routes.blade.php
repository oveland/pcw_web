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
                <div class="col-md-4 text-center {{ $route->as_group ? 'hide' : '' }}">
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
                    @if(!$route->as_group)
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
                    @endif
                </div>

                <div class="col-md-12 p-t-10 {{ $route->as_group ? 'hide' : '' }}">
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

                @if($route->controlPoints && !$route->as_group)
                    @include('migrations._controlPoints')
                @endif

                <div class="compare-{{ $route->id }} text-left"></div>
            </div>
        </div>
    </div>
</div>
<!-- end panel -->