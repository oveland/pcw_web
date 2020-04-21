@if(count($passengers))
    @php($strTime = new \App\Http\Controllers\Utils\StrTime())
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
                <i class="ion-clipboard"></i> @lang('List counter passengers')
                @include('reports.booths.historic.totalInfo')
                @include('partials.pagination.totalInfo',['paginator' => $passengers ])
            </h5>
        </div>
        <div class="tab-content panel p-0">
            @include('reports.booths.historic._tableHistory')
            <div class="col-md-12 p-0">{{ $passengers->links() }}</div>
        </div>
    </div>
    <script>hideSideBar()</script>
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups')!</strong></h4>
            <hr class="hr">
            @lang('The are not list of passengers and counter on this date range')
        </div>
    </div>
@endif