@extends(Auth::user()->isProprietary() || $hideMenu ? 'layouts.blank' : 'layout')

@section('stylesheets')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.css') }}" rel="stylesheet" type="text/css" />

    <script src="https://www.pcwserviciosgps.com/plugins/lodash.min.js"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    <style>
        .slider-player .irs-single {
            top: 20px;
            padding-top: 3px;
            height: 20px;
            font-size: 1rem;

            border-radius: 40px;
            width: auto;
            min-width: 35px;
            text-align: center;
            font-weight: bold;
            z-index: 100;
            box-shadow: 0 0 0px 2px #7a0045;
            background-image: linear-gradient(to right, #73008a, #9c0087, #bf0081, #df0077, #f9006b) !important;
        }

        .slider-player .irs-bar-edge {
            background: #73008a !important;
        }

        .slider-player .irs-bar {
            background-image: linear-gradient(to right, #73008a, #6c35ab, #5d54c8, #6c35ab, #73008a) !important;
        }

        .slider-player .irs-single::after {
            border: none !important;
        }

        .speed-player {
            position: absolute;
            bottom: -34px;
            background: #00000073;
            border-radius: 100px !important;
        }

        .speed-player .irs {
            height: 18px !important;
            margin-top: 5px;
            margin-bottom: 5px !important;
        }

        .speed-player .irs-single {
            top: 2px;
            padding-top: 8px;
            border-radius: 40px;
            width: 15px;
            height: 15px;
            text-align: center;
            font-size: 0rem;
            font-weight: bold;
            z-index: 100;
            box-shadow: 0 0 2px 1px #f90052;
            background: #ff9600 !important;
            background-image: linear-gradient(to right, #bdff00, #edcd00); !important;
        }

        .speed-player .irs-bar {
            background-image: linear-gradient(to right, #8aff00, #d4cf00, #fb9900, #ff5b1c, #f90052) !important;
        }

        .speed-player .irs-bar-edge {
            background: #8aff00 !important;
        }

        .speed-player .irs-bar-edge, .speed-player .irs-line-left {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .speed-player .irs-max, .speed-player .irs-min, .speed-player .irs-from, .speed-player .irs-to {
            display: none !important;
        }

        .speed-player .irs-line-left, .speed-player .irs-line-right, .speed-player .irs-line-mid {
            height: 8px;
        }

        .speed-player .irs-line, .speed-player .irs-bar, .speed-player .irs-bar-edge, .speed-player .irs-slider {
            top: 5px;
            height: 8px;
        }

        .speed-player .irs-line, .speed-player .irs-bar, .speed-player {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .speed-player .irs-single::after {
            border: none !important;
            background: yellow !important;
        }

        .play-controls {
            margin-bottom: 5px;
        }

        .play-controls .btn {
            font-size: 1.2rem !important;
            color: #609aac;
            box-shadow: 0 0 0px 3px #df0077 !important;
            margin: 3px !important;
            transition: all ease-in-out 1s !important;
        }

        .play-controls .btn-pause {
            color: #b750a5;
            box-shadow: 0 0 0px 3px #df0077 !important;
        }

        .play-controls .btn-xs {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        .play-controls .btn {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .range-reports {
            z-index: 1;
            padding-top: 10px;
            padding-bottom: 5px;
            background: rgba(37, 37, 37, 0.6);
            color: white;
            position: absolute;
            width: 100%;
        }

        .range-reports .irs-bar, .range-reports .irs-bar-edge, .range-reports .irs-single {
            background: #f57c1e;
        }

        .btn-historic-info {
            padding-left: 10px !important;
            padding-right: 10px !important;
            font-size: 0.8em !important;
            margin-bottom: 5px !important;
        }

        .passengers-frame-container, .passengers-frame {
            clear: left !important;
            background: #4c4c4c;
            font-family: Consolas, monaco, monospace !important;
            padding-right: 10px;
            padding-left: 10px;
            font-size: 1.1rem;
            color: lightgrey;
            margin-top: 5px;
            border-radius: 5px
        }

        .btn-passengers-info {
            width: 100%;
            display: block;
            /*position: absolute !important;*/
            right: 0;
            /*top: 82px;*/
            font-size: 1.7rem !important;
        }

        .passengers-label {
            display: block;
            text-shadow: 0px 0px 8px #000000;
            font-weight: bolder;
        }

        .passengers-label.text-lime, small.text-lime {
            color: #56ff00;
        }

        .passengers-label.text-warning, small.text-warning {
            color: #ff6e29;
        }

        .passengers-label i, small i {
            margin-left: 2px;
        }

        .info-trips-container {
            background: rgba(0, 0, 0, 0.5);
            width: auto;
            display: table;
            transition: all ease 500ms;
            position: absolute;
            top: 85px;
            left: 0;
        }

        .info-trips li.active {
            color: #d8ff00 !important;
        }

        img.photo {
            cursor: pointer;
            z-index: 10000 !important;
            margin: 2px;
            box-shadow: rgb(0 0 0 / 43%) 0px 10px 7px 2px;
            transition: all 0.3s ease-in-out;
            -webkit-transition: all 0.3s ease-in-out;
            -moz-transition: all 0.3s ease-in-out;
            -o-transition: all 0.3s ease-in-out;
        }

        img.photo.enlarge {
            width: 48%;
            height: auto;
            position: fixed;
            z-index: 10000 !important;
            top: 10%;
            left: 25%;
        }

        .photo-container {
            position: absolute;
            bottom: 0;
        }

        .photos-image-container {
            position: absolute;
            top: 0;
            width: 100%;
        }

        .photo-info {
            position: absolute;
            z-index: 100000;
            width: auto;
            display: inline-block;
            background: #000000b5;
            color: white;
            padding: 10px;
            bottom: 5px;
            border-radius: 5px;
            left: 10px;
        }

        .color-white {
            color: white !important;
        }

        .text-counted {
            color: #00d6ff !important;
        }

        .text-gray {
            color: #3b3b3b !important;
        }

        .form-search-report .form-group {
            margin-bottom: 5px;
        }

        @media only screen and (max-width: 1200px) {
            .btn-passengers-info {

            }
        }

        .help-block {
            margin-top: 5px !important;
        }

        #photo-show {
            position: absolute;
            bottom: 0;
            margin: 15px 15px 1px;
        }

        #photo-show .photo-id-container {
            background: #F1C40F;
            color: white;
            padding-left: 2px;
            padding-right: 2px;
            border-radius: 10px;
            font-size: 1.2rem;
            margin-top: 2px;
        }

        @media only screen and (max-width: 600px) {
            .range-reports {
            z-index: 1;
            padding-top: 10px;
            padding-bottom: 5px;
            background: rgba(37, 37, 37, 0.9);
            color: white;
            position: relative;
            width: 100%;
            margin: 0 !important;
            }

            .slider-player .irs-single {
                top: 19px;
                padding-top: 5px;
                height: 25px;
                font-size: 1.2rem;
            }

            .show-info-last{
                padding-top: 5px !important;
                height: 20px;
            }

            .passengers-label {
                display: inline-flex;
                padding-left: 5px;
            }

            .passengers-label span{
                margin-left: 5px;
            }

            .panel {
                box-shadow: none;
            }
            .panel .panel-body {
                padding: 0;
            }

            .form-actions{
                text-align: center !important;
            }

            .historic-container{
                padding: 0 !important;
            }

            .range-reports {
                padding-top: 15px !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .info-trips-container {
                width: 100%;
                background: transparent;
                position: initial;
                margin-top: 30px !important;
            }

            .irs-grid {
                display: none !important;
            }

            .speed-player {
                position: initial;
                background: transparent;
            }

            .slider-player .irs {
                height: auto !important;
                margin-bottom: 8px;
            }

            .slider-player .irs-max, .slider-player .irs-min, .slider-player .irs-from, .slider-player .irs-to {
                display: none !important;
            }

            .slider-player .irs-line, .slider-player .irs-bar, .slider-player .irs-bar-edge, .slider-player .irs-slider.single {
                top: 5px !important;
            }

            .slider-player .irs-single {
                top: -2px !important;
            }
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right hidden-xs">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Historic')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Historic')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-route-historic-search') }}">
            <div class="panel panel-inverse m-0">
                <div class="panel-body p-5">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-2" ontouchstart="pause()">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="">@lang('Select an option')</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->canSelectRouteReport())
                            <div class="col-md-2" ontouchstart="pause()">
                                <div class="form-group">
                                    <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                    <div class="form-group">
                                        <select name="route-report" id="route-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                            @include('partials.selects.routes', compact('routes'), ['withAll' => "true"])
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2" ontouchstart="pause()">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12">
                                        @include('partials.selects.vehicles', compact('vehicles'))
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 hide" ontouchstart="pause()">
                            <div class="form-group">
                                <label for="type-report" class="control-label">@lang('Options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="type-report" name="type-report" type="checkbox" value="group-vehicles" checked> @lang('Group')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" ontouchstart="pause()">
                            <div class="form-group">
                                <label for="date-report" class="control-label field-required">@lang('Date report')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ $dateReport ? $dateReport : date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 form-actions hidden-xs" ontouchstart="pause()">
                            <div class="form-group">
                                <label class="control-label hidden-xs"><br></label>
                                <div class="form-group">
                                    <button type="submit" onclick="$('#export').val('')" class="btn btn-success btn-search-report">
                                        <i class="fa fa-map-o"></i> @lang('Search')
                                    </button>
                                    <a href="#" class="btn btn-lime btn-export form-export" style="display: nonse">
                                        <i class="fa fa-file-excel-o"></i> @lang('Export')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <input id="time-range-report" name="time-range-report" type="text" value="" />
                        </div>

                        <div class="col-md-3 col-sm-6 col-xs-12 form-actions hidden-sm hidden-md hidden-lg" ontouchstart="pause()">
                            <div class="form-group m-0">
                                <label class="control-label hidden-xs"><br></label>
                                <div class="form-group m-0">
                                    <button type="submit" onclick="$('#export').val('')" class="btn btn-success btn-search-report btn-rounded btn-outline" style="margin-top: 5px">
                                        <i class="fa fa-map-o"></i> @lang('Search')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->

        <!-- begin content report -->
        <div class="loading-report col-md-12 m-t-40"></div>

        <div class="historic-container col-md-12">
            <div class="col-md-12 col-sm-12 col-xs-12 p-0" style="display: grid">
                <div class="range-reports col-md-12 p-t-2" style="display: grid">
                    <div class="slider-player">
                        <div class="text-center hidden-xs" style="position: absolute;width: 100%; top: 3px">
                            <label for="slider-player">
                                <small class="text-muted">Deslice para reproducir recorrido</small>
                            </label>
                        </div>
                        <input id="slider-player" type="text" />
                    </div>
                    <div class="help-block text-white show-info m-0">
                        <div class="col-md-4 col-sm-12 col-xs-12 p-0 hidden-sxs">
                            <span class="hidden-xs">
                                <i class="fa fa-map-o"></i> <span class="total">0</span> @lang('reports')
                            </span>
                            <span class="hidden-xs">
                                @lang('between') <i class="fa fa-clock-o"></i> <span class="time-from">--:--:--</span> - <i class="fa fa-clock-o"></i> <span class="time-to">--:--:--</span>
                            </span>
                        </div>

                        <div class="col-md-4 col-sm-12 col-xs-12 p-0 text-center">
                            <div class="play-controls text-center">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <span class="btn btn-default btn-xs btn-circle btn-backward tooltipss m-0 faa-parent animated-hover" data-placement="left" title="@lang('Backward')"  onclick="backward(maxTrack/10, true)" ontouchstart="backward(maxTrack/10, true)">
                                        <i class="fa fa-fast-backward faa-passing-reverse"></i>
                                    </span>

                                    <span class="btn btn-default btn- btn-circle btn-play tooltipss" title="@lang('Play')"  onclick="pause()" ontouchstart="pause()">
                                        <i class="fa fa-play faa-flash animated"></i> <span class="time"></span>
                                    </span>

                                    <span class="btn btn-default btn- btn-circle btn-pause tooltipss" style="display: none" title="@lang('Pause')" onclick="play()" ontouchstart="play()">
                                        <i class="fa fa-pause faa-flash animated"></i> <span class="time"></span>
                                    </span>

                                    <span class="btn btn-default btn-xs btn-circle btn-forward tooltipss m-0 faa-parent animated-hover" data-placement="right" title="@lang('Forward')"  onclick="forward(maxTrack/10, true)" ontouchstart="forward(maxTrack/10, true)">
                                        <i class="fa fa-fast-forward faa-passing"></i>
                                    </span>
                                </div>

                                <div class="speed-player col-lg-6 col-lg-offset-3 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                                    <input id="speed-player" type="text"/>
                                </div>
                            </div>

                            @if(Auth::user()->company->hasSensorCounter())
                                <div class="col-md-12 col-xs-12 btn-passengers-info tooltips active" data-placement="bottom" title="@lang('Count passengers')">
                                    @if(Auth::user()->isAdmin() && false)
                                        <small class="passengers-label p-0 hidden-xs" style="display: block">
                                            <span class="passengers-frame-container">
                                                <i class="fa fa-clock-o"></i>
                                                <span class="passengers-frame p-0">Frame counter</span>
                                            </span>
                                        </small>
                                    @endif

                                    <div class="hide">
                                        <span class="passengers-label">
                                            <i class="fa fa-users"></i> <span class="hidden-xs">@lang('Total'):</span> <span class="passengers-total"></span>
                                        </span>
                                        <small class="passengers-label hide">
                                            <i class="fa fa-angle-double-up"></i> <span class="hidden-xs">@lang('Ascents'):</span> <span class="passengers-total-ascents"></span>
                                        </small>

                                        <small class="passengers-label hide">
                                            <i class="fa fa-angle-double-down"></i> <span class="hidden-xs">@lang('Descents'):</span> <span class="passengers-total-descents"></span>
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4 col-sm-12 col-xs-12 p-0 text-right show-info-last">
                            <span class="btn btn-default btn-xs btn-circle btn-historic-info show-info-route tooltips" data-title="@lang('Route') | @lang('Mileage') @lang('route')"><i class="fa fa-flag"></i> <span class="route"></span> | <span class="mileage-route">0</span> Km</span>
                            <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" title="@lang('Speed')"><i class='fa fa-tachometer'></i> <span class="speed">0</span> Km/h</span>
                            <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" title="@lang('Mileage') @lang('in the day')"><i class='fa fa-road'></i> <span class="current-mileage">0</span> Km</span>
                            <span class="btn btn-default btn-xs btn-circle btn-historic-info status-vehicle tooltips" title="@lang('Vehicle status')"><i class='fa fa-send'></i></span>
                        </div>
                    </div>

                    @if(Auth::user()->company->hasSensorCounter())
                        <div class="m-t-5 p-20 p-t-0 p-b-0 info-trips-container show-info">
                            <h5 class="text-bold m-b-0">
                                <i class="fa fa-users"></i> @lang('Histórico de pasajeros'):
                                <span class="info-trips-total">0</span>
                                <span class="hide">
                                        (<span class="passengers-total"></span>
                                        <small class="text-lime hide">
                                            <i class="fa fa-angle-double-up"></i> <span class="passengers-total-ascents"></span>
                                        </small>
                                        <small class="text-warning hide">
                                            <i class="fa fa-angle-double-down"></i> <span class="passengers-total-descents"></span>
                                        </small>)
                                    </span>
                            </h5>
                            <div class="text-center">
                                <div class="label label-warning tooltips" title="@lang('Total charge')" style="display: inline-block;margin-top: 5px">
                                    <small style="font-weight: bold !important;color: white;font-size: 1.1rem;">
                                        <i class="fa fa-dollar"></i> <span class="hidden-xs hide">@lang('Total charge'):</span> <span class="passengers-total-charge"></span>
                                    </small>
                                    <hr class="m-0">
                                    <small style="font-weight: bold !important;color: white;font-size: 1.1rem;" class="passengers-tariff-charges">

                                    </small>
                                </div>
                            </div>
                            <div style="display: flex">
                                <div class="info-trips"></div>
                                <div style="align-items: flex-end; display: flex;padding-left: 5px">
                                    <small class="passengers-label text-lime hide" style="display: none">
                                        <i class="fa fa-angle-double-up"></i> <span class="hidden-xs hide">@lang('Ascents'):</span> <span class="passengers-route-ascents"></span>
                                    </small>

                                    <small class="passengers-label text-warning hide" style="display: none">
                                        <i class="fa fa-angle-double-down"></i> <span class="hidden-xs hide">@lang('Descents'):</span> <span class="passengers-route-descents"></span>
                                    </small>

                                    <small class="passengers-label text-info tooltips" style="display: none;margin-left: 5px" title="@lang('Tariff')">
                                        <i class="fa fa-tag"></i> <span class="passengers-route-tariff"></span>
                                    </small>
                                </div>
                            </div>

                            <div class="photo-alerts" style="display: grid"></div>

                            @if(Auth::user()->isAdmin() && false)
                                <small class="passengers-label p-0 hidden-sm hidden-md hidden-lg" style="display: block">
                                        <span class="passengers-frame-container">
                                            <i class="fa fa-clock-o"></i>
                                            <span class="passengers-frame p-0">Frame counter</span>
                                        </span>
                                </small>
                            @endif

                            @if(Auth::user()->isAdmin())
                            <div class="play-controls text-center m-t-5">
                                <span class="btn btn-default btn-xs btn-circle btn-backward tooltips m-0 faa-parent animated-hover event event-1" data-placement="bottom" title="@lang('Hasta evento de activación')" onclick="setEvent(1)" data-color="green-jungle" ontouchstart="setEvent(1)">
                                    <i class="fa fa-user text-lime"></i>
                                </span>

                                <span class="btn btn-default btn-xs btn-circle btn-backward tooltips m-0 faa-parent animated-hover event event-2" data-placement="bottom" title="@lang('Hasta evento de conteo')" onclick="setEvent(2)" data-color="green" ontouchstart="setEvent(2)">
                                    <i class="fa fa-user text-info"></i>
                                </span>

                                <span class="btn btn-default btn-xs btn-circle btn-backward tooltips m-0 faa-parent animated-hover event event-3" data-placement="bottom" title="@lang('Hasta evento de activación o conteo')" onclick="setEvent(3)" data-color="warning"  ontouchstart="setEvent(3)">
                                    <i class="fa fa-user text-warning"></i>
                                </span>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>


            <div id="google-map-light-dream" class="col-md-12 col-sm-12 col-xs-12 p-0 map-report-historic"></div>

            @if(Auth::user()->company->hasSensorCounter())
            <div class="col-md-6 col-sm-12 col-xs-12 p-0 photo-container">

                <div id="photo-show" style="display: none">
                    <button class="btn btn-warning btn-outline btn-circle pulse text-center" onclick="largePhoto = true; pause()" ontouchstart="largePhoto = true; pause()">
                        <i class="fa fa-camera"></i>
                    </button>
                    @if(Auth::user()->company->hasSensorCounter())
                        <div class="photo-id-container text-center">
                            <small class="photo-id"></small>
                        </div>
                    @endif
                </div>

                <img id="photo-loading" class="photo" draggable="false" style="display: none" src="https://satsangiconsultancy.files.wordpress.com/2019/01/gif-final.gif" alt="" width="20%">

                <div id="photos-container" class="show-info" style="width: 100%">
                    <div class="photos-image-container">

                    </div>

                    <div class="photo-info">
                        <div class="photo-passengers-label hide">
                            <i class="fa fa-users"></i> <span class="hidden-xss">@lang('Total'):</span> <span class="photo-passengers-total"></span>
                        </div>
                        <div class="photo-passengers-label hide">
                            <i class="fa fa-users"></i> <span class="hidden-xss">@lang('Round trip'):</span> <span class="photo-passengers-trip"></span>
                        </div>
                        <div class="photo-passengers-label">
                            <span class="photo-time hide"></span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- end content report -->
    </div>
    <!-- end row -->
@endsection

@section('scripts')

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/lib/markdown.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    @include('template.google.maps')

    @include('reports.route.historic.templates._script')

    <template id="marker-animation-scripts"></template>

    <script type="application/javascript">
        // Play controls:
        let track = 0;
        let period = 150;
        let tracking = false;
        let pauseOnEvent = null;
        let maxTrack = 0;
        let trackInterval = null;
        let controls = $('.play-controls');

        fitHeight('#google-map-light-dream');

        // controls.hide();

        let reportRouteHistoric = null;
        let reportContainer = $('.historic-container');
        let loadingReport = $('.loading-report');
        $('.menu-routes, .menu-report-route-historic').addClass('active-animated');

        const vehicleReport = '{{ $vehicleReport }}';
        const companyReport = '{{ $companyReport }}';
        let form = $('.form-search-report');

        let trackIndex = 0;
        let largePhoto = false;
        let imgLarge = false;

        // Function to set image dimensions
        function toggleImgSize(img) {
            if(imgLarge) {
                resetImg(img);
            }else {
                enlargeImg(img);
            }
        }

        function enlargeImg(img) {
            $('.photo-image').removeClass('enlarge');
            $(img).addClass('enlarge');
            imgLarge = true;
        }

        function resetImg(img) {
            $('.photo-image').removeClass('enlarge');
            $(img).removeClass('enlarge');
            imgLarge = false;
        }

        function loadScript(url, callback)
        {
            // Adding the script tag to the head as suggested before
            var head = document.head;
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = url;

            // Then bind the event to the callback function.
            // There are several events for cross browser compatibility.
            script.onreadystatechange = callback;
            script.onload = callback;

            // Fire the loading
            head.appendChild(script);
        }

        const autoLoad = function(){
            //const vehicleReport = $('#vehicle-report').val();
            setTimeout(()=>{
                if (vehicleReport) form.submit();
            },1400);
        };

        $(document).ready(function () {
            initializeMap(() => {
                reportRouteHistoric = new ReportRouteHistoric(map);
                loadScript("https://www.pcwserviciosgps.com/google/plugins/jquery.easing.min.js", function(){
                    loadScript("https://www.pcwserviciosgps.com/google/plugins/markerAnimate.js", function(){
                        loadScript("https://www.pcwserviciosgps.com/google/plugins/SlidingMarker.min.js", function(){
                            SlidingMarker.initializeGlobally();
                            fitHeight('#google-map-light-dream');
                        });
                    });
                });
            });

            form.submit(function (e) {
                let btnExport = $('.btn-export').fadeOut();
                e.preventDefault();
                if (form.isValid()) {
                    stop();
                    form.find('.btn-search-report').addClass(loadingClass);

                    // reportContainer.hide();
                    loadingReport.fadeIn();
                    reportRouteHistoric.clearMap();
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (report) {
                            fitHeight('#google-map-light-dream');
                            reportRouteHistoric.processHistoricReportData(report);

                            $('#slider-player').data("ionRangeSlider").update({
                                min: 0,
                                max: report.total,
                                from: 0
                            });

                            setTimeout(()=>{
                                if( report.total > 0 ) {
                                    btnExport.fadeIn();

                                    maxTrack = report.total;
                                    controls.fadeIn(2000);
                                    play();
                                }
                                btnExport.attr('href', report.exportLink);
                            },1000);

                            hideSideBar();
                        },
                        complete: function() {
                            form.find('.btn-search-report').removeClass(loadingClass);
                            loadingReport.hide();
                            reportContainer.slideDown();
                        }
                    });
                }
            });

            $('#route-report').change(function () {
                loadSelectVehicleReportFromRoute($(this).val(), vehicleReport, autoLoad);
                // reportContainer.slideUp(100);
            });

            $('#vehicle-report').change(function () {
                const infoRoute = $('.show-info-route');
                const infoTrips = $('.info-trips-container');

                if($(this).val() == 1199 || $(this).val() == 1217 || $(this).val() == 1233 || $(this).val() == 1893 || $(this).val() == 2312 || $(this).val() == 1357 || $(this).val() == 1351 || $(this).val() == 1873 || $(this).val() == 2357) {
                    infoRoute.slideUp(100);
                    infoTrips.slideDown();

                    if($(this).val() == 1217) {
                        $('#time-range-report').data("ionRangeSlider").update({
                            from: 54,
                            to: 252
                        });
                    }
                }else {
                    infoRoute.slideDown();
                    infoTrips.slideUp(100);
                }


            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), false, vehicleReport, autoLoad);
                    loadSelectRouteReport($(this).val());

                    $('.btn-passengers-info').hide();
                    if( $(this).val() == 17 || $(this).val() == 14 ) {
                        $('.btn-passengers-info').slideDown();
                    }

                }).val(companyReport ? companyReport : 14).change();
            @else
                $('#route-report').change();
            @endif

            let time = moment('00:00', 'HH:mm');
            let timeRange = [];
            for(let min = 0; min <= (24*60-2); min+=5){
                timeRange.push(time.format('HH:mm'));
                time.add(5, 'minutes');
            }
            timeRange.push(time.subtract(1, 'minutes').format('HH:mm'));

            const initialTime = parseInt('{{ $initialTime ? $initialTime : 60 }}');
            const finalTime = parseInt('{{ $finalTime ? $finalTime : 144 }}');

            $("#time-range-report").ionRangeSlider({
                type: "double",
                from: initialTime,
                to: finalTime,
                values: timeRange,
                drag_interval: true,
                //max_interval: 48,
                prefix: "<i class='fa fa-clock-o'></i> ",
                skin: "modern",
                decorate_both: true,
                prettify: true,
                keyboard: false,
                grid_num: 10,
                values_separator: " → ",
                onChange: function (slider) {
                }
            });

            $('#slider-player').ionRangeSlider({
                skin: "round",
                grid: true,
                prettify: true,
                prefix: "<i class='fa fa-location-arrow faa-burst animated-hover'></i> ",
                skin: "modern",
                keyboard: false,
                min: 0,
                max: 1,
                from: 0,
                step: 1,
                onStart: function(data) {

                },
                onUpdate: function (slide) {
                    trackIndex = slide.from;
                    reportRouteHistoric.updateBusMarker(slide.from);
                },
                onChange: function(slide){
                    if (tracking) {
                        pause(true);
                    }
                    reportRouteHistoric.updateBusMarker(slide.from);
                    // setTrack(slide.from);
                },
                onFinish: function(slide){
                    track = slide.from;
                    if (tracking) play();
                    else reportRouteHistoric.showPhotos(slide.from);
                }
            });

            const speed = parseInt('{{ $speed ? $speed : 280 }}');

            $('#speed-player').ionRangeSlider({
                skin: "round",
                keyboard: false,
                min: 50,
                max: 500,
                from: speed,
                step: 1,
                onStart: function(data) {
                    period = 500 - speed;
                },
                onUpdate: function (slide) {

                },
                onChange: function(slide){
                    period = 500 - slide.from;
                    if (tracking) play();
                },
                onFinish: function(slide){

                }
            });

            loadingReport.html($('#animated-loading').html()).hide();

            $('body').on('mousedown', '.range-reports .irs-line', function() {
                pause(tracking);
            }).on('mousedown', '.range-reports .irs-bar', function() {
                pause(tracking);
            }).on('mousedown', '.range-reports .irs-single', function() {
                pause(tracking);
            }).on('touchstart', '.range-reports .irs-single', function() {
                pause(tracking);
            });

            @if(Auth::user()->isProprietary())
            setTimeout(() => {
                $('#vehicle-report').change();
            },2000);
            @endif

            $('#photos-container').hide();
        });

        var min = 0;
        var max = 1000;
        var marks = [0, 1];

        function convertToPercent(num) {
            return (num - min) / (max - min) * 100;
        }

        function addMarks($slider) {
            var html = '';
            var left = 0;
            var left_p = "";
            var i;

            for (i = 0; i < marks.length; i++) {
                left = convertToPercent(marks[i]);
                left_p = left + "%";
                html += '<span class="showcase__mark" style="left: ' + left_p + '">';
                html += marks[i];
                html += '</span>';
            }

            $slider.append(html);
        }

        function play() {
            tracking = true;
            clearTrack();
            setTrackInterval(period, 1);

            controls.find('.btn-pause').hide();
            controls.find('.btn-play').show();
        }

        function stop(setTracking) {
            pause(setTracking);
            setTrack(0);
        }

        function pause(setTracking) {
            tracking = setTracking ? setTracking : false;
            clearTrack();

            controls.find('.btn-pause').show();
            controls.find('.btn-play').hide();

            reportRouteHistoric.showPhotos(trackIndex);
        }


        function backward(times, force) {
            let back = track - (times ? times : 1);
            back = back >= 0 ? back : track;
            setTrack(back, force);

            if(!tracking) {
                reportRouteHistoric.showPhotos(trackIndex);
            }
        }

        function forward(times, force) {
            let next = track + (times ? times : 1);
            if (next <= maxTrack) {
                setTrack(next, force);
            } else {
                pause(true);
            }

            const events = reportRouteHistoric.getEvents(next);
            
            if (pauseOnEvent && events.includes(pauseOnEvent)) {
                pause();
            }

            if(!tracking) {
                reportRouteHistoric.showPhotos(trackIndex);
            }
        }

        function setTrack(from, force) {
            track = from;
            if (tracking || force) $('#slider-player').data("ionRangeSlider").update({from});
        }

        function setTrackInterval(period, fw) {
            trackInterval = setInterval(() => {
                forward(fw);
            }, period);
        }

        function setEvent(number) {
            $('.event').each(function(i, e) {
                const c = $(this).data('color');
                $(this).removeClass('bg-' + c).find('i').removeClass('faa-bounce').removeClass('animated').removeClass('color-white')
            });

            let button = $('.event-' + number);
            const color = button.data('color');
            if (pauseOnEvent != number) {
                button.find('i').addClass('faa-bounce').addClass('animated').addClass('color-white');
                button.addClass('bg-' + color);
                pauseOnEvent = number;
            } else {
                pauseOnEvent = null;
            }

            reportRouteHistoric.setPauseOnEvent(pauseOnEvent);
        }

        function clearTrack() {
            if (trackInterval) {
                clearInterval(trackInterval);
            }
        }
    </script>

    <style>
        .pulse {
            font-size: 1.3rem !important;
            cursor: pointer;
            box-shadow: 0 0 0 rgba(204,169,44, 0.4);
            animation: pulse 2s infinite;
            border-radius: 100px !important;
            padding: 12px !important;
        }
        .pulse:hover {
            animation: none;
        }

        @-webkit-keyframes pulse {
            0% {
                -webkit-box-shadow: 0 0 0 0 rgba(204,169,44, 0.4);
            }
            70% {
                -webkit-box-shadow: 0 0 0 30px rgba(204,169,44, 0);
            }
            100% {
                -webkit-box-shadow: 0 0 0 0 rgba(204,169,44, 0);
            }
        }
        @keyframes pulse {
            0% {
                -moz-box-shadow: 0 0 0 0 rgba(204,169,44, 0.4);
                box-shadow: 0 0 0 0 rgba(204,169,44, 0.4);
            }
            70% {
                -moz-box-shadow: 0 0 0 30px rgba(204,169,44, 0);
                box-shadow: 0 0 0 30px rgba(204,169,44, 0);
            }
            100% {
                -moz-box-shadow: 0 0 0 0 rgba(204,169,44, 0);
                box-shadow: 0 0 0 0 rgba(204,169,44, 0);
            }
        }

        .irs-with-grid {
            height: 36px !important ;
        }
    </style>
@endsection
