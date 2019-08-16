@php
    $badge=[
        'Inicial' => 'success',
        'Final' => 'warning',
        'Normal' => 'primary'
    ];
@endphp
<div class="col-sm-12 col-xs-12 col-md-12">
    <ul class="list-group">
        @foreach($route->controlPoints->sortBy('order') as $controlPoint)
            @php
                $controlPointTimes = \App\Models\Routes\ControlPointTime::whereControlPointId($controlPoint->id)->orderBy('day_type_id')->get();
            @endphp
            <li class="list-group-item text-muted text-left bg-{{$loop->first && $controlPoint->type !='Inicial'?'danger':''}} bg-{{$loop->last && $controlPoint->type !='Normal'?'danger':''}}"
                data-toggle="collapse" data-target="#control-point-{{ $controlPoint->id }}">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <button class="btn btn-sm btn-rounded">{{ $controlPoint->order }} <i class="fa fa-map-marker text-{{$controlPoint->trajectory==0?'lime':'warning'}}"></i></button>
                        <div class="col-md-12 p-0 m-t-5 text-center">
                            <span class="badge badge-{{$badge[$controlPoint->type]}}">
                                <i class="icon-directions" aria-hidden="true"></i> {{ $controlPoint->type }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <strong>({{ $controlPoint->id }}) {{ $controlPoint->name }}</strong>
                        <hr class="m-t-5 m-b-5">
                        <div class="m-t-2">
                            <div class="pull-left m-t-5">
                                <span title="@lang('Distance from dispatch')" class="tooltips">
                                    <i class="fa fa-flag-checkered text-purple" aria-hidden="true"></i> {{ $controlPoint->distance_from_dispatch }} m
                                </span>
                                <i class="fa fa-ellipsis-v m-r-10 m-l-10 text-muted" aria-hidden="true"></i>
                                <span title="@lang('Distance to next point')" class="tooltips">
                                    <i class="icon-direction text-info" aria-hidden="true"></i> {{ $controlPoint->distance_next_point }} m
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 p-0">
                        <div class="tab-content p-2">
                            @php
                                $controlPointTimesByDay = $controlPointTimes->groupBy(function ($controlPoint, $key){
                                    return $controlPoint->fringe->dayType->id;
                                });
                            @endphp

                            @foreach($controlPointTimesByDay as $dayTypeId => $controlPointTimes)
                                @php
                                    $dayType = \App\Models\Routes\DayType::find($dayTypeId);
                                @endphp
                                <div class="m-t-3 tab-pane fade {{ $loop->first ? 'active in':'' }} day-type-{{ $dayTypeId }}-{{ $route->id }}" title="@lang('Time from dispatch') - {{ $dayType->description ?? 'None' }}">
                                    @php
                                        $controlPointTimes = $controlPointTimes->sortBy(function($controlPointTime){
                                            return $controlPointTime->fringe->from;
                                        });
                                    @endphp
                                    @foreach($controlPointTimes as $controlPointTime)
                                        @php
                                            $fringe = $controlPointTime->fringe;
                                        @endphp
                                        <span data-title="<i class='fa fa-clock-o fa-spin text-warning'></i> {{ $controlPointTime->time }} <br> @lang('From') {{ $fringe->from }} @lang('to') {{ $fringe->to }}" data-placement="bottom" data-html="true"
                                              class="badge badge-warning m-t-1 m-b-1 tooltips">
                                            {{ $controlPointTime->time_from_dispatch ?? 'None' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </li>
            <div id="control-point-{{ $controlPoint->id }}" class="collapse text-left m-o" style="border: none">

            </div>
        @endforeach
    </ul>
</div>