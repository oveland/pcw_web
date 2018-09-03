@if( $dispatches->isNotEmpty() )

<div class="modal fade" id="modal-reassign-route">
    <div class="modal-dialog modal-sm">
        <form id="form-reassign-route" class="col-md-12" action="{{ route('operation-dispatch-auto-dispatcher-reassign-route') }}">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">
                        <i class="fa fa-car"></i>
                        <span id="vehicle-to-reassign" class="tooltips" title="@lang('Vehicle')">000</span> <span class="text-muted">|</span> @lang('Reassign route')
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                            <input type="hidden" id="form-reassign-route-dispatcher-vehicle" name="dispatcher_vehicle_id">
                            <input type="hidden" id="form-reassign-route-vehicle" name="vehicle_id">
                            <div class="form-input-flat">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="new-route" class="control-label field-required">@lang('Route')</label>
                                        <div class="form-group">
                                            <select name="route_id" id="new-route" class="default-select2 form-control col-md-12">
                                                <option value="null">@lang('Select an option')</option>
                                                @foreach($routes as $route)
                                                    <option value="{{$route->id}}">{{ $route->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn width-100 btn-default" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn width-100 btn-info">@lang('Update')</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if( $unassignedVehicles->isNotEmpty() )
<div class="panel panel-inverse">
    <div class="panel-heading click" data-toggle="collapse" data-target="#unassigned-vehicles">
        <h4 class="panel-title text-bold">
            <span class="fa-stack" style="top:-3px">
                <i class="fa fa-ban fa-stack-2x text-muted" style="font-size: 220%"></i>
                <i class="fa fa-bus fa-stack-1x"></i>
            </span>
            {{ $unassignedVehicles->count() }} @lang('unassigned vehicles')
        </h4>
    </div>
    <div id="unassigned-vehicles" class="panel-body collapse">
        <div class="row">
            @foreach($unassignedVehicles as $vehicle)
                <div class="col-md-1 p-5">
                    <button class="btn btn-sm btn-default col-md-12 btn-reassign-route"
                            data-toggle="modal" data-target="#modal-reassign-route"
                            onclick="
                                    $('#form-reassign-route-dispatcher-vehicle').val(0);
                                    $('#form-reassign-route-vehicle').val({{ $vehicle->id }});
                                    $('#new-route').val('').change();
                                    $('#vehicle-to-reassign').text('{{ $vehicle->number }}')">
                        <i class="fa fa-car text-muted"></i> {!! $vehicle->number !!}
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row p-10">
    <div class="col-md-12">
        <ul class="nav nav-pills nav-pills-success">
            @foreach($dispatches as $dispatch)
                <li class="{{ $loop->first ? 'active' : '' }}">
                    <a href="#dispatch-{{ $dispatch->id }}" data-toggle="tab">
                        <i class="fa fa-map-signs"></i> {{ $dispatch->name }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content panel row" style="background: rgba(0,34,47,0.09)">
            @foreach($dispatches as $dispatch)
                <div id="dispatch-{{ $dispatch->id }}" class="tab-pane fade {{ $loop->first ? 'active in' : '' }}">
                    @php( $dispatcherVehicles = $dispatch->dispatcherVehicles)
                    @if( $dispatcherVehicles->isNotEmpty() )
                        @php($dispatcherVehiclesByRoutes = $dispatcherVehicles->sortBy(function($dispatcherVehicle){
                            return $dispatcherVehicle->route->name;
                        })->groupBy('route_id'))
                        @foreach($dispatcherVehiclesByRoutes as $routeId => $dispatcherVehicles)
                            @php( $dispatcherVehicles = $dispatcherVehicles->sortBy(function($dispatcherVehicle){
                                return $dispatcherVehicle->vehicle->number;
                            }) )
                            @php($route = $dispatcherVehicles->first()->route)
                            <div class="col-md-4">
                                <div class="widget">
                                    <div class="widget-header bg-inverse">
                                        <h4 class="text-white">
                                            <i class="fa fa-flag"></i> {{ $route->name }}
                                        </h4>
                                    </div>
                                    <div class="row">
                                        @foreach($dispatcherVehicles as $dispatcherVehicle)
                                            @php($vehicle = $dispatcherVehicle->vehicle)
                                            <div class="col-md-3 p-5">
                                                <button class="btn btn-sm btn-default col-md-12 btn-reassign-route"
                                                        data-toggle="modal" data-target="#modal-reassign-route"
                                                        onclick="
                                                                $('#form-reassign-route-dispatcher-vehicle').val({{ $dispatcherVehicle->id }});
                                                                $('#form-reassign-route-vehicle').val({{ $vehicle->id }});
                                                                $('#new-route').val({{ $route->id }}).change();
                                                                $('#vehicle-to-reassign').text('{{ $vehicle->number }}')">
                                                    <i class="fa fa-car text-muted"></i> {!! $vehicle->number !!}
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @include('partials.alerts.noRegistersFound')
                    @endif
                </div>
            @endforeach
        </div>

    </div>
</div>

<script type="application/javascript">
    $('.default-select2').select2();
    let formReassignRoute = $('#form-reassign-route');
    let modalReassignRoute = $('#modal-reassign-route');
    formReassignRoute.submit(function(event){
        event.preventDefault();
        if( formReassignRoute.isValid() ){
            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: 'POST',
                dataType:'json',
                success:function (response) {
                    if(response.success){
                        gsuccess(response.message);
                        modalReassignRoute.modal('hide');
                        $('.form-search-operation').submit();
                    }else{
                        gerror(response.message);
                    }
                },
                complete:function () {

                }
            });
        }
    });
</script>
@else
    @include('partials.alerts.noRegistersFound')
@endif