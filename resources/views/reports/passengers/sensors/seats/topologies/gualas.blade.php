<style>
    .seat-inactive {
        background: forestgreen
    }

    .seat-inactive:hover {
        background: #1a6b1a
    }

    .seat-active {
        background: #005da8
    }

    .seat-active:hover {
        background: #003661
    }

    .seat-top-guala {
        width: 40px;
        height: 20px;
        border: none;
        border-radius: 0;
        margin: -5px -3px -2px -2px;
    }
    .seat-driver{
        width: 20px !important;
        margin-left: 5px !important;
    }
    .car-top-guala{
        padding-left: 0;
        padding-right: 10px !important;
        border-right: 15px solid #babbc1;
        border-top: 3px solid #ebebeb;
        border-bottom: 3px solid #ebebeb;
        border-radius: 10px 25px 25px 10px;
        margin-top:20px;
        width: 250px;
    }
    .slider-horizontal{
        width: 100% !important;
        position: relative !important;
        top: -15px !important;
        background: transparent !important;
    }
    .slider-selection, .slider-track{
        background: transparent !important;
    }
</style>

<div class='row btn btn-default car-top-guala'>
    {{--START ROW 1--}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-11 col-sm-11 col-xs-11 data-row1 no-padding' style='border-right: 1px solid grey;'>
            @foreach ($seatingStatus['row1'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-guala tooltips' data-title='{{ $seat }}'>
                    &nbsp;&nbsp;
                </span>
            @endforeach
        </div>

        <div class='col-md-1 col-sm-1 col-xs-1 no-padding tooltips' data-original-title='@lang("Driver's seat")' data-placement='left'>
            <span class='btn btn-xs btn-warning seat-top-guala seat-driver'>
                <i class='fa fa-user-circle-o'></i>
            </span>
        </div>
    </div>
    {{-- END ROW 1 --}}

    {{-- START ROW CENTER --}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-11 col-sm-11 col-xs-11' style='padding: 2px 8px 0 2px;border-right: 1px solid grey'>
            <span class='btn btn-xs btn-default col-md-12 hex-seating'>
                {{ $hexSeating }}
            </span>
        </div>
        <div class='col-md-1 col-sm-1 col-xs-1 no-padding data-center'>
            @foreach ($seatingStatus['center'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-guala seat-driver tooltips' data-title='{{ $seat }}'>&nbsp;
                    &nbsp;
                </span>
            @endforeach
        </div>
    </div>
    {{-- END ROW CENTER --}}

    {{-- START ROW 3 --}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-11 col-sm-11 col-xs-11 data-row2 no-padding' style='border-right: 1px solid grey'>
            @foreach ($seatingStatus['row2'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-guala tooltips' data-title='{{ $seat }}'>
                    &nbsp;
                </span>
            @endforeach
        </div>

        <div class='col-md-1 col-sm-1 col-xs-1 no-padding data-window'>
            @foreach ($seatingStatus['window'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-guala seat-driver tooltips' data-title='{{ $seat }}'>&nbsp;
                    &nbsp;
                </span>
            @endforeach
        </div>
    </div>
    {{-- END ROW 3 --}}
</div>

<script type='application/javascript'>$('.tooltips').tooltip({container: 'body'});</script>