@php($badge=[
    'Inicial' => 'success',
    'Final' => 'warning',
    'Normal' => 'primary'
])
<div class="col-sm-12 col-xs-12 col-md-8">
    <ul class="list-group">
        @foreach($route->controlPoints->sortBy('order') as $controlPoint)
            @php($controlPointTimes = \App\ControlPointTime::whereControlPointId($controlPoint->id)->orderBy('day_type_id')->get())
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
                    <div class="col-md-5">
                        <strong>{{ $controlPoint->name }}</strong>
                        <hr class="m-t-5 m-b-5">
                        <div class="m-t-2">
                            <div class="pull-left m-t-5">
                                <span title="@lang('Distance from dispatch')"><i class="fa fa-flag-checkered text-purple" aria-hidden="true"></i> {{ $controlPoint->distance_from_dispatch }} m</span>
                                <i class="fa fa-ellipsis-v m-r-10 m-l-10 text-muted" aria-hidden="true"></i>
                                <span title="@lang('Distance to next point')"><i class="icon-direction text-info" aria-hidden="true"></i> {{ $controlPoint->distance_next_point }} m</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 p-0">
                        <div class="col-md-12">
                            <div class="m-t-3">
                                <i class="fa fa-calendar-check-o text-info" aria-hidden="true"></i>
                                <span title="@lang('Time from dispatch') - @lang('Business day')" class="label label-inverse">
                                    {{ isset($controlPointTimes[0])?$controlPointTimes[0]->time_from_dispatch:'None' }}
                                </span>
                                <i class="fa fa-clock-o fa-spin text-danger m-5" aria-hidden="true"></i>
                                <span title="@lang('Time to next point') - @lang('Business day')" class="label label-inverse">
                                    {{ isset($controlPointTimes[0])?$controlPointTimes[0]->time_next_point:'None' }}
                                </span>
                            </div>
                            <div class="m-t-3">
                                <i class="fa fa-calendar-check-o text-purple" aria-hidden="true"></i>
                                <span title="@lang('Time from dispatch') - @lang('Saturday day')" class="label label-grey">
                                    {{ isset($controlPointTimes[0])?$controlPointTimes[1]->time_from_dispatch:'None' }}
                                </span>
                                <i class="fa fa-clock-o fa-spin text-danger m-5" aria-hidden="true"></i>
                                <span title="@lang('Time to next point') - @lang('Saturday day')" class="label label-grey">
                                    {{ isset($controlPointTimes[0])?$controlPointTimes[1]->time_next_point:'None' }}
                                </span>
                            </div>
                            <div class="m-t-3">
                                <i class="fa fa-calendar-check-o text-warning" aria-hidden="true"></i>
                                <span title="@lang('Time from dispatch') - @lang('Public holiday')" class="label label-default">
                                    {{ isset($controlPointTimes[0])?$controlPointTimes[2]->time_from_dispatch:'None' }}
                                </span>
                                <i class="fa fa-clock-o fa-spin text-danger m-5" aria-hidden="true"></i>
                                <span title="@lang('Time to next point') - @lang('Public holiday')" class="label label-default">
                                    {{ isset($controlPointTimes[0])?$controlPointTimes[2]->time_next_point:'None' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </li>
            <div id="control-point-{{ $controlPoint->id }}" class="collapse text-left m-o" style="border: none">
                {{ dump($controlPoint->toArray(),$controlPointTimes->toArray()) }}
            </div>
        @endforeach
    </ul>
</div>