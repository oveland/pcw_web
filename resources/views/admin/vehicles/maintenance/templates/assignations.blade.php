@php( $unAssignedVehicles = $maintenanceVehiclesReports->where('hasMaintenanceReports',false) )
@php( $assignedVehicles = $maintenanceVehiclesReports->where('hasMaintenanceReports',true) )
<!-- begin fullcalendar -->
<div class="fullcalendar vertical-box m-b-20">
    <!-- begin fullcalendar-event -->
    <div class="fullcalendar-event vertical-box-column hidden-sm hidden-xs">
        <div id="external-events">
            <h5 class="m-t-0 m-b-15">
                <div class="dropdown">
                    <span class="dropdown-toggle btn btn-default text-left" type="button" data-toggle="dropdown" style="width: 100%">
                        @lang('Unassigned') <i class="fa fa-angle-down pull-right"></i>
                        <span class="badge badge-danger pull-right m-l-15 total-unassigned">{{ count($unAssignedVehicles) }}</span>
                    </span>
                    <ul class="dropdown-menu">
                        <li>
                            <a onclick="event.preventDefault();deleteMaintenanceDates();" class="btn">
                                <i class="fa fa-undo"></i>
                                @lang('Reset')
                            </a>
                        </li>
                    </ul>
                </div>
            </h5>
            @foreach($unAssignedVehicles as $unassignedVehicle)
                <div class="fc-event" data-id="{{ $unassignedVehicle->vehicleId }}">
                    <div class="fc-event-icon">
                        <i class="fa fa-bus fa-fw text-warning-light"></i>
                    </div>
                    {{ $unassignedVehicle->vehicleNumber }} <i class="fa fa-credit-card-alt"></i> <span class="hide">🚍</span> {{ $unassignedVehicle->vehiclePlate }}
                </div>
            @endforeach
        </div>
    </div>
    <!-- end fullcalendar-event -->
    <!-- begin fullcalendar-container -->
    <div class="fullcalendar-container vertical-box-column">
        <div id='calendar'></div>
    </div>
    <!-- end fullcalendar-container -->
</div>
<!-- end fullcalendar -->
<script type="application/javascript">
    var containerCalendar = $('.fullcalendar-container');
    var initCalendar = function() {
        "use strict";
        var assignedVehicles = JSON.parse('{!! collect($assignedVehicles->values())->toJson() !!}');
        var assignments = [];

        for(var index in assignedVehicles){
            var assignedVehicle = assignedVehicles[index];
            var maintenanceReport =assignedVehicle.maintenanceReport;

            for(var indexM in maintenanceReport){
                var maintenance = maintenanceReport[indexM];
                assignments.push(
                    {
                        id: maintenance.id,
                        title: assignedVehicle.vehicleNumber+' 🚍 '+assignedVehicle.vehiclePlate,
                        start: moment(maintenance.date).format('YYYY-MM-DD')
                    }
                );
            }
        }

        $('#external-events .fc-event').each(function() {
            $(this).data('event', {
                id: $(this).data('id'),
                title: $.trim($(this).text()),
                stick: true
            });

            $(this).draggable({
                zIndex: 999,
                revert: true,      // will cause the event to go back to its
                revertDuration: 0,  //  original position after the drag
                stop: function( event, ui ) {
                    setTimeout(function(){
                        var totalUnassigned = $('#external-events').find('.fc-event').length;
                        $('.total-unassigned').text(totalUnassigned);
                    },100);
                }
            });
        });

        $('#calendar').fullCalendar({
            weekNumbersWithinDays:true,
            weekends:true,
            weekNumbers :false,
            header: {
                left: 'basicWeek,month',
                center: 'title',
                right: 'prev,today,next '
            },
            views: {
                basicWeek: { // name of view
                    titleFormat: ''
                },
                title: { // name of view
                    titleFormat: 'MMMM'
                }
            },
            defaultView: 'basicWeek',
            droppable: true, // this allows things to be dropped onto the calendar
            drop: function(date, jsEvent, ui, resourceId) {
                var event = $(this).data('event');
                createMaintenanceDate(event.id,date.format('YYYY-MM-DD'));
                $(this).remove();
            },
            selectable: true,
            selectHelper: true,
            select: function(start, end) {
            },
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: assignments,
            eventDrop: function(event, delta, revertFunc) {
                updateMaintenanceDate(event.id,event.start.format('YYYY-MM-DD'));
            }
        });
    };

    var createMaintenanceDate = function (vehicleId, date) {
        containerCalendar.block({message: $('#loading').html()});
        var updateRoute = '{{ route('admin-vehicles-maintenance-create',[':VEHICLE_ID']) }}';
        $.ajax({
            url: updateRoute.replace(':VEHICLE_ID',vehicleId),
            type: 'POST',
            data: {
                date: date
            },
            success: function (data) {
                if (data !== '0') gsuccess('@lang('Maintenance date created successfully')');
                else gerror('@lang('Maintenance date is not created'). @lang('Contact your administrator')');
            },
            error: function () {
                gerror('@lang('An error occurred in the process. Contact your administrator')');
            },
            complete: function () {
                animateSubmit = false;
                $('.form-search-report').submit();
            }
        });
    };

    var updateMaintenanceDate = function (maintenanceId, date) {
        containerCalendar.block({message: $('#loading').html()});
        var updateRoute = '{{ route('admin-vehicles-maintenance-update',[':MAINTENANCE_ID']) }}';
        $.ajax({
            url: updateRoute.replace(':MAINTENANCE_ID',maintenanceId),
            type: 'PUT',
            data: {
                date: date
            },
            success: function (data) {
                if (data !== '0') ginfo('@lang('Maintenance date updated successfully')');
                else gerror('@lang('Maintenance date is not updated'). @lang('Contact your administrator')');
            },
            error: function () {
                gerror('@lang('An error occurred in the process. Contact your administrator')');
            },
            complete: function () {
                animateSubmit = false;
                $('.form-search-report').submit();
            }
        });
    };

    var deleteMaintenanceDates = function () {
        containerCalendar.block({message: $('#loading').html()});
        var companyId = null;
        @if(Auth::user()->isAdmin())
            companyId = $('#company-report').val();
        @endif
        var confirm = window.confirm('@lang('Reset')?');
        var deleteRoute = '{{ route('admin-vehicles-maintenance-delete',[':COMPANY_ID']) }}';
        if (confirm) {
            $('.main-container').slideUp(100);
            $.ajax({
                url: deleteRoute.replace(':COMPANY_ID', companyId),
                type: 'DELETE',
                success: function (data) {
                    if( data !== 'error' )gsuccess(data+' @lang('Maintenance dates deleted successfully')');
                    else gerror('@lang('Maintenance dates not deleted'). @lang('Contact your administrator')');
                },
                error: function () {
                    gerror('@lang('An error occurred in the process. Contact your administrator')');
                },
                complete: function () {
                    $('.form-search-report').submit();
                }
            });
        }else{
            $('.form-search-report').submit();
        }
    };

    $(document).ready(function () {
        setTimeout(function () {
            initCalendar();
        }, 400);
    });
</script>