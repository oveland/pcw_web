@if( $dispatches->isNotEmpty() )

<div class="modal fade" id="modal-reassign-route">
    <div class="modal-dialog modal-md">
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
                            <input type="hidden" id="form-delete-auto-dispatcher" name="delete" value="false">
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
                    <button id="btn-form-delete" type="submit" class="btn btn-sm width-100 btn-danger pull-left" onclick="$('#form-delete-auto-dispatcher').val(true)">
                        <i class="fa fa-ban"></i>
                        @lang('Unassign')
                    </button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                    <button type="submit" class="btn btn-sm width-100 btn-info" onclick="$('#form-delete-auto-dispatcher').val(false)">
                        <i class="fa fa-save"></i>
                        @lang('Update')
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-inverse">
    <div class="panel-heading">
        <div class="panel-title text-bold row">
            <div class="pull-left col-md-6 col-sm-12 col-xs-12 m-t-5">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <input id="search-vehicle" min="1" max="9999" type="number" title="@lang('Search')" placeholder="@lang('Search')" class="form-control col-md-12 col-sm-12 col-xs-12">
                </div>
                <div class="col-md-9 col-sm-6 col-xs-12 text-muted legend-search"></div>
            </div>
            <div class="pull-right col-md-6 col-sm-12 col-xs-12 text-right p-t-5 click" data-toggle="collapse" data-target="#unassigned-vehicles">
                @if( $unassignedVehicles->isNotEmpty() )
                    <span class="fa-stack" style="top:-3px">
                        <i class="fa fa-ban fa-stack-2x text-muted" style="font-size: 220%"></i>
                        <i class="fa fa-bus fa-stack-1x"></i>
                    </span>
                    {{ $unassignedVehicles->count() }} @lang('unassigned vehicles')
                @else
                    <i class="fa fa-check"></i>
                    @lang('All vehicles are assigned')
                @endif
            </div>
        </div>
    </div>
    @if( $unassignedVehicles->isNotEmpty() )
        <div id="unassigned-vehicles" class="panel-body collapse">
            <div class="row">
                @foreach ($unassignedVehicles as $vehicle)
                    <div class="col-md-3 col-sm-4 col-xs-6 p-5">
                        <button class="btn btn-sm btn-default col-md-12 col-sm-12 col-xs-12 btn-reassign-route unassigned-vehicle tooltips" data-title="{{ $vehicle->id }}"
                                data-toggle="modal" data-target="#modal-reassign-route"
                                onclick="
                                        $('#btn-form-delete').hide();
                                        $('#form-reassign-route-dispatcher-vehicle').val({{ $vehicle->dispatcherVehicle ? $vehicle->dispatcherVehicle->id : 0 }});
                                        $('#form-reassign-route-vehicle').val({{ $vehicle->id }});
                                        $('#new-route').val('').change();
                                        $('#vehicle-to-reassign').text('{{ $vehicle->number }}')">
                            <i class="fa fa-car text-muted"></i> {!! $vehicle->number !!}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="row p-10">
    <div class="col-md-12">
        <ul class="nav nav-pills nav-pills-success">
            @foreach($dispatches as $dispatch)
                <li class="{{ $loop->first ? 'active' : '' }}">
                    <a href="#dispatch-{{ $dispatch->id }}" data-toggle="tab" class="text-center">
                        <i class="fa fa-map-signs"></i> {{ $dispatch->id }} - {{ $dispatch->name }}<br>
                        <small>
                            <i class="fa fa-car"></i> {{ $dispatch->dispatcherVehicles->count() }} @lang('Vehicles')
                        </small>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="dispatches tab-content panel row" style="background: rgba(0,34,47,0.09)">
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
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="widget">
                                    <div class="widget-header bg-inverse">
                                        <h4 class="text-white">
                                            <i class="fa fa-flag"></i> {{ $route->id }} - {{ $route->name }}<br>
                                            <small class="text-white">
                                                <i class="fa fa-car"></i> {{ $dispatcherVehicles->count() }} @lang('Vehicles')
                                            </small>
                                        </h4>
                                    </div>
                                    <div class="row p-l-20 p-r-20">
                                        @foreach($dispatcherVehicles as $dispatcherVehicle)
                                            @php
                                                $vehicle = $dispatcherVehicle->vehicle;
                                                $defaultDispatcherVehicle = $dispatcherVehicle->defaultDispatcherVehicle;
                                            @endphp
                                            <div class="col-md-1 col-sm-4 col-xs-6 p-5 tooltips" data-title="{!! $vehicle->id !!} - ({!! $vehicle->plate !!}): {{ $defaultDispatcherVehicle ? ($defaultDispatcherVehicle->route->name): 'SIN RUTA POR DEFECTO' }}" data-placement="bottom">
                                                <button class="btn btn-sm btn-{{ $defaultDispatcherVehicle ? 'default' :'danger' }} col-md-12 col-sm-12 col-xs-12 btn-reassign-route vehicle-{{ $vehicle->number }}"
                                                        data-toggle="modal" data-target="#modal-reassign-route"
                                                        data-route-name="{{ $route->name }}" data-dispatch-name="{{ $dispatch->name }}"
                                                        onclick="
                                                                $('#btn-form-delete').hide().fadeIn(2000);
                                                                $('#form-reassign-route-dispatcher-vehicle').val({{ $dispatcherVehicle->id }});
                                                                $('#form-reassign-route-vehicle').val({{ $vehicle->id }});
                                                                $('#new-route').val({{ $route->id }}).change();
                                                                $('#vehicle-to-reassign').text('{{ $vehicle->number }}')">
                                                    <i class="fa fa-car text-{{ $dispatcherVehicle->default ? 'success':'warning' }}"></i> {!! $vehicle->number !!}
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

    $('#search-vehicle').keyup(searchVehicle).change(searchVehicle);

    function searchVehicle() {
        let search = $('#search-vehicle').val();
        let legendSearch = $('.legend-search').hide();
        if (search) {
            let vehicle = $('.vehicle-' + search);
            let unassignedVehicle = $('.unassigned-vehicle-' + search);
            if (vehicle.length > 0) {
                legendSearch.html("<strong>@lang('Dispatch'):</strong> " + vehicle.data('dispatch-name') + "<br> <strong>@lang('Route'):</strong> " + vehicle.data('route-name'));
            } else if (unassignedVehicle.length > 0) {
                legendSearch.html('@lang('Unassigned')');
            } else{
                legendSearch.html('@lang('Not found')');
            }
        } else {
            legendSearch.empty();
        }
        legendSearch.fadeIn();
    }
</script>
@else
    @include('partials.alerts.noRegistersFound')
@endif