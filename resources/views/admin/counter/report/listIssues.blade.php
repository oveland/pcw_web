@if(count($counterIssues))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-rounded btn-info" data-toggle="collapse" data-target=".collapse-frame">
                    <i class="ion-ios-search"></i>
                    @lang('See all frames')
                </a>
            </div>
            <h5 class="text-white label-vehicles m-b-0">
                <i class="fa fa-exclamation-triangle"></i> @lang('List of counter issues')
                @include('partials.pagination.totalInfo',['paginator' => $counterIssues ])
            </h5>
        </div>
        <div class="tab-content panel p-0">
            <div class="">
                    <div class="table-responsive">
                        <!-- begin table -->
                        <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle table-report">
                            <thead>
                            <tr class="inverse">
                                <th>
                                    <i class="fa fa-list text-muted"></i><br>
                                    @lang('N°')
                                </th>
                                <th>
                                    <i class="fa fa-car text-muted"></i><br>
                                    @lang('Vehicle')
                                </th>
                                <th>
                                    <i class="fa fa-users text-muted"></i><br>
                                    @lang('Passengers')
                                </th>
                                <th>
                                    <i class="fa fa-exclamation-triangle text-muted"></i><br>
                                    @lang('Items issues')
                                </th>
                                <th>
                                    <i class="fa fa-camera text-muted"></i><br>
                                    @lang('Inactive cameras')
                                </th>
                                <th>
                                    <i class="ion-radio-waves"></i><br>
                                    @lang('Check counter')
                                </th>
                                <th>
                                    <i class="fa fa-rocket text-muted"></i><br>
                                    @lang('Actions')
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($counterIssues as $counterIssue)
                                @include('admin.counter.report._issue')
                            @endforeach
                            </tbody>
                        </table>
                        <!-- end table -->
                        <div class="col-md-12">{{ $counterIssues->links() }}</div>
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