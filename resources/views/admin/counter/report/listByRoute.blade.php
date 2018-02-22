@if(count($passengersByRoundTrip))
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
                <i class="ion-flag"></i> @lang('List counter passengers by route')
                @include('partials.pagination.totalInfo',['paginator' => $passengersByRoundTrip ])
            </h5>

            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills nav-pills-success">
                        @foreach($passengersByRoundTrip as $roundTrip => $passengers )
                            <li class="{{ $loop->first?'active':'' }} tooltips" data-title="@lang('Round Trip') {{ $roundTrip }}. @lang('Total'): {{ $passengers->last()->total -  $passengers->first()->total }}">
                                <a href="#tab-round-trip-{{ $roundTrip }}" data-toggle="tab">
                                    <i class="fa fa-retweet"></i> {{ $roundTrip }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content panel p-0">
            @foreach($passengersByRoundTrip as $roundTrip => $passengers )
                @include('admin.counter.report._tableHistory')
            @endforeach
            <div class="col-md-12 p-0">{{ $passengersPerRoundTrip->links() }}</div>
        </div>
    </div>
    <script>hideSideBar()</script>

    <div class="modal fade" id="modal-counter-issue">
        <div class="modal-dialog modal-lg" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="row">
                        <h4 class="modal-title">
                            <blockquote class="m-0">
                                <h3 class="m-3 text-danger">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    @lang('Counter issue')
                                </h3>
                            </blockquote>
                        </h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="tab-content panel">
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
                                        <tbody class="container-issue">
                                        </tbody>
                                    </table>
                                    <!-- end table -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer hide" style="width:90%;">
                    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.btn-show-counter-issue').click(function(){
            var btn = $(this);
            var modal = $('#modal-counter-issue');
            var modalContainer = modal.find('.container-issue');

            modalContainer.hide();
            modal.modal('show');
            modalContainer.load(btn.data('action'),function(){
                modalContainer.fadeIn(2000);
            });
        });
    </script>
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