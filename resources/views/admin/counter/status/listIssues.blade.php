@if(count($counterIssues))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white label-vehicles">
                <i class="fa fa-exclamation-triangle"></i>
                @lang('List of counter issues')
            </h5>
        </div>
        <div class="tab-content panel">
            <div class="row">
                <div class="table-responsive">
                    <!-- begin table -->
                    <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle">
                        <thead>
                        <tr class="inverse">
                            <th>@lang('Passengers')</th>
                            <th>@lang('Vehicle')</th>
                            <th>@lang('Items issues')</th>
                            <th>@lang('Inactive cameras')</th>
                            <th>@lang('Check counter')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($counterIssues as $counterIssue)
                            @php($vehicle = $counterIssue->vehicle)
                            @php($itemsIssues = collect(json_decode($counterIssue->items_issues,true)))
                            <tr class="bg-inverse text-white text-bold">
                                <td>{{ $counterIssue->date }}</td>
                                <td colspan="4">{{ $counterIssue->frame }}</td>
                            </tr>
                            <tr>
                                <td>{{ $counterIssue->total }}</td>
                                <td>{{ $vehicle->number }}</td>
                                <td width="30%">
                                    @foreach($itemsIssues as $item => $issues)
                                        <div class="widget widget-stat bg-warning text-white p-5 m-b-5">
                                            <div class="row">
                                                <div class="col-md-3 text-right p-0 m-t-5 p-r-5" style="border-right: 1px dotted white">
                                                    <p class="text-uppercase text-bold m-0">@lang('Item') {{ $item }}</p>
                                                </div>
                                                <div class="col-md-9 p-0 m-0">
                                                    <ul>
                                                        @foreach($issues as $name => $issue)
                                                            <li style="text-align: left">
                                                                <small class="text-white text-bold" style="float: left">@lang($name):
                                                                    @if(is_array($issue))
                                                                        <ul>
                                                                            @foreach($issue as $field => $value)
                                                                                <li>
                                                                                    F[{{ $field }}] = {{ $value }}
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <cite title="">{{ $issue }}</cite>
                                                                    @endif
                                                                </small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @if($counterIssue->raspberry_cameras_issues)
                                        <div class="btn btn-danger btn-sm">
                                            {{ $counterIssue->raspberry_cameras_issues }}
                                        </div>
                                    @endif
                                </td>
                                <td width="30%">
                                    @if($counterIssue->raspberry_check_counter_issue)
                                        <div class="btn btn-primary btn-sm">
                                            {{ $counterIssue->raspberry_check_counter_issue }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
    <script>hideSideBar()</script>
@else
    <div class="alert alert-success alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Hey')!</strong></h4>
            <hr class="hr">
            @lang('The company haven´t issues in your counters at the selected date')
        </div>
    </div>
@endif