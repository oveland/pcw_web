@php( $unAssignedPeakAndPlates = $peakAndPlateReports->where('assigned',false) )
@php( $assignedPeakAndPlates = $peakAndPlateReports->where('assigned',true) )
<!-- begin fullcalendar -->
<div class="fullcalendar vertical-box m-b-20">
    <!-- begin fullcalendar-event -->
    <div class="fullcalendar-event vertical-box-column hidden-sm hidden-xs">
        <div id="external-events">
            <h5 class="m-t-0 m-b-15">
                <div class="dropdown">
                    <span class="dropdown-toggle btn btn-default text-left" type="button" data-toggle="dropdown" style="width: 100%">
                        @lang('Unassigned') <i class="fa fa-angle-down pull-right"></i>
                        <span class="badge badge-danger pull-right m-l-15 total-unassigned">{{ count($unAssignedPeakAndPlates) }}</span>
                    </span>
                    <ul class="dropdown-menu">
                        <li>
                            <a onclick="resetAssignments();" class="btn">
                                <i class="fa fa-undo"></i>
                                @lang('Reset')
                            </a>
                        </li>
                    </ul>
                </div>
            </h5>
            @foreach($unAssignedPeakAndPlates as $unAssignedPeakAndPlate)
                @php($vehicle = \App\Models\Vehicles\Vehicle::find($unAssignedPeakAndPlate->vehicleId))
                <div class="fc-event" data-id="{{ $vehicle->id }}">
                    <div class="fc-event-icon">
                        <i class="fa fa-car fa-fw text-success-light"></i>
                    </div>
                    {{ $vehicle->number }} <i class="fa fa-credit-card-alt"></i> {{ $vehicle->plate }}
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
    var initCalendar = function() {
        "use strict";
        var assignedPeakAndPlates = JSON.parse('{!! collect($assignedPeakAndPlates->values())->toJson() !!}');
        var assignments = [];

        for(var index in assignedPeakAndPlates){
            var assignedPeakAndPlate = assignedPeakAndPlates[index];
            var assignedDate = moment(assignedPeakAndPlate.date);
            //assignedDate.subtract({days:7*5});
            for (var i = 1; i <= parseInt(200 / 7); i++) {
                assignments.push(
                    {
                        id: assignedPeakAndPlate.vehicleId,
                        title: assignedPeakAndPlate.vehicleNumber+' '+assignedPeakAndPlate.vehiclePlate,
                        start: assignedDate.add({days: i == 1 ? 0 : 5}).format('YYYY-MM-DD')
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
                updateWeekDay(event.id,date.format('YYYY-MM-DD'));
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
                updateWeekDay(event.id,event.start.format('YYYY-MM-DD'));
                /*if (!confirm("Are you sure about this change?")) {
                    revertFunc();
                }*/
            }
        });
    };

    var updateWeekDay = function(vehicleId,date){
      $.ajax({
          url: '{{ route('admin-vehicles-peak-and-plate-update') }}',
          type: 'POST',
          data:{
            vehicleId: vehicleId,
            date: date
          },
          success:function(data){
              console.log(data);
          }
      });
    };

    var resetAssignments = function () {
        var companyId = null;
        @if(Auth::user()->isAdmin())
            companyId = $('#company-report').val();
        @endif
        var confirm = window.confirm('@lang('Reset')?');
        if( confirm ){
            $('.main-container').slideUp(100);
            $.ajax({
                url: '{{ route('admin-vehicles-peak-and-plate-reset') }}',
                type: 'POST',
                data:{
                    companyId: companyId
                },
                success:function(data){
                    console.log(data);
                },
                complete:function(){
                    $('.form-search-report').submit();
                }
            });
        }
        return false;
    };

    $(document).ready(function() {
        setTimeout(function(){
            initCalendar();
        },400);
    });
</script>