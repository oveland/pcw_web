<style>
    .seat-top-taxcentral{
        height:14px !important;
        border: 1px solid !important;
        margin:0px !important;
    }
    .seat-top-taxcentral i{
        font-size: 90% !important;
    }
    .car-top-taxcentral{
        padding: 0 10px 0px 5px !important;
        border-right: 15px solid #babbc1;
        border-top: 3px solid #ebebeb;
        border-bottom: 3px solid #ebebeb;
        border-radius: 10px 25px 25px 10px;
        margin-top:10px;
        width: 230px;
    }
    .seat-icon{
        width: 30px;
    }
    .seat-icon-window{
        width: 23px;
        height: 20px !important;
        position: absolute;
        bottom: 5px !important;
        left: 5px;
    }
    .seat-icon-window span{
        width: 23px;
        border-bottom-right-radius: 11px;
    }
    .seat-icon-driver{
        width: 23px;
        height: 20px !important;
        position: absolute;
        top: 8px !important;
        left: 5px;
    }
    .seat-icon-driver span{
        border-top-right-radius: 11px;
    }
    .gm-style-iw-d{
        width: 270px !important;
        height: auto !important;
    }

    .div-back-seat-1>span:nth-of-type(1){
        position:absolute;
        height:15px !important;
        top:3px;
        z-index:1;
    }
    .div-back-seat-1>span:nth-of-type(2){
        margin-left:32px !important;
    }

    .div-back-seat-2>span:nth-of-type(1){
        position:absolute;
        height:15px !important;
        top:6px;
        z-index:1;
    }
    .div-back-seat-2>span:nth-of-type(2){
        margin-left:32px !important;
    }

    .div-back-seat-3>span:nth-of-type(1){
        position:absolute;
        height:15px !important;
        bottom:6px;
        z-index:1;
    }
    .div-back-seat-3>span:nth-of-type(2){
        margin-left:32px !important;
    }

    .div-back-seat-4>span:nth-of-type(1){
        position:absolute;
        height:15px !important;
        z-index:1;
        bottom: 0;
    }
    .div-back-seat-4>span:nth-of-type(2){
        margin-left:32px !important;
    }
    .seat-inactive {
        background: forestgreen !important;
    }
    .seat-active {
        background: #005da8 !important;
    }
    .hex-seating{
        font-size: 0.7em !important;
    }
</style>

<div class='btn btn-default car-top-taxcentral md-skip'>
    {{--START ROW 1--}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-10 col-sm-10 col-xs-10 no-padding text-left div-seat-top-taxcentral div-back-seat-1' style='border-right: 1px solid grey;'>
            @foreach ($seatingStatus['row1'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-icon seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-taxcentral div-back-seat-1 tooltips' data-title='{{ $seat }}'></span>
            @endforeach
        </div>

        <div class='col-md-2 col-sm-2 col-xs-2 no-padding tooltips' data-original-title='@lang("Driver's seat")' data-placement='left'>
            <span class='btn btn-xs btn-danger seat-top-taxcentral seat-icon-driver'>
                <i class='fa fa-user-circle-o'></i>
            </span>
        </div>
    </div>
    {{-- END ROW 1 --}}
    {{-- START ROW 2 --}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-10 col-sm-10 col-xs-10 no-padding text-left div-seat-top-taxcentral div-back-seat-2' style='border-right: 1px solid grey'>
            @foreach ($seatingStatus['row2'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-icon seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-taxcentral div-back-seat-2 tooltips' data-title='{{ $seat }}'></span>
            @endforeach
        </div>

        <div class='col-md-2 col-sm-2 col-xs-2 no-padding'>
        </div>
    </div>
    {{-- END ROW 2--}}

    {{-- START ROW CENTER --}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-12 col-sm-12 col-xs-12' style='padding: 2px 8px 0 2px;border-right: 1px solid grey'>
            <span class='btn btn-xs btn-default col-md-12 hex-seating'>
                {{ $hexSeating }}
            </span>
        </div>
    </div>
    {{-- END ROW CENTER --}}

    {{-- START ROW 3 --}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-10 col-sm-10 col-xs-10 no-padding text-left div-seat-top-taxcentral div-back-seat-3' style='border-right: 1px solid grey'>
            @foreach ($seatingStatus['row3'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-icon seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-taxcentral div-back-seat-3 tooltips' data-title='{{ $seat }}'></span>
            @endforeach
        </div>

        <div class='col-md-2 col-sm-2 col-xs-2 no-padding'>
        </div>
    </div>
    {{-- END ROW 3--}}

    {{-- START ROW 4 --}}
    <div class='col-md-12 no-padding'>
        <div class='col-md-10 col-sm-10 col-xs-10 no-padding text-left div-seat-top-taxcentral div-back-seat-4' style='border-right: 1px solid grey'>
            @foreach ($seatingStatus['row4'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-icon seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-taxcentral div-back-seat-4 tooltips' data-title='{{ $seat }}'></span>
            @endforeach
        </div>

        <div class='col-md-2 col-sm-2 col-xs-2 no-padding'>
            @foreach ($seatingStatus['window'] as $seat => $status)
                <span id="seat-{{ $seat }}" class='btn btn-xs seat-icon-window seat-{{ $status == 1 ? 'active':'inactive' }} seat-top-taxcentral tooltips' data-title='{{ $seat }}'></span>
            @endforeach
        </div>
    </div>
    {{-- END ROW 4--}}
</div>

<script type='application/javascript'>$('.tooltips').tooltip({container: 'body'});</script>