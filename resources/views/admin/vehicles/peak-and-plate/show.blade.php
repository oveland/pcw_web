<div class="panel">
    <ul class="nav nav-tabs nav-tabs-primary nav-justified">
        <li class="active">
            <a href="#justified-tab-1" data-toggle="tab">
                <i class="fa fa-sliders" aria-hidden="true"></i> @lang('Assignations') / <i class="fa fa-calendar" aria-hidden="true"></i> @lang('Calendar')
            </a>
        </li>
        <li class="">
            <a href="#justified-tab-2" data-toggle="tab">
                <i class="fa fa-calendar-times-o" aria-hidden="true"></i>
                @lang('Public Holidays')
            </a>
        </li>
    </ul>
    <div class="tab-content m-b-0">
        <div class="tab-pane fade active in" id="justified-tab-1">
            @include('admin.vehicles.peak-and-plate.templates.assignations')
        </div>
        <div class="tab-pane fade" id="justified-tab-2">
            @include('admin.vehicles.peak-and-plate.templates.holidays')
        </div>
    </div>
</div>