@extends('layout')

@php
    $rel = $crop->w / $crop->h;
    $wPreview = 120;
    $hPreview =$wPreview * $rel;
@endphp

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')
    <!-- begin row -->
    <div class="row">
        <div class="text-center">
            <div class="col-md-12 text-center">
                <img src='{{ $imageOrig->encode('data-url')->encoded }}' width='{{ $width }}px' height='{{ $height }}px'/>
            </div>

            <hr class="hr col-md-12 no-padding">

            <div class="col-md-6 col-md-offset-3 text-center p-10">
                <img src='{{ $image->encode('data-url')->encoded }}' width='{{ $wPreview }}px' height='{{ $hPreview }}px' style="border: 3px solid red"/>
            </div>

            <div class="col-md-2 col-md-offset-3 text-center">
                <div style='background: {{ $refColorHex }}; height: 100px; width: 100px;border-radius: 50%;box-shadow: -2px 0px 3px 1px #7e7e7e;margin: auto' class="text-center">
                    <span class="text-white" style="position: relative;top: 38%;">
                        <span>{{ $refColorHex }}</span><br>
                        <small class="text-white" style="font-size: 0.7em;">@lang('Reference')</small>
                    </span>
                </div>
            </div>

            <div class="col-md-2 text-center">
                <div style='background: {{ $colorHex }}; height: 100px; width: 100px;border-radius: 50%;box-shadow: -2px 0px 3px 1px #7e7e7e;margin: auto' class="text-center">
                    <span class="text-white" style="position: relative;top: 38%;">
                        <span>{{ $colorHex }}</span><br>
                        <small class="text-white" style="font-size: 0.7em;">{{ $equals ? "SIMILAR" : "Different" }}</small>
                    </span>
                </div>
            </div>

            <div class="col-md-2 text-center">
                <div style='background: {{ $equals ? "#a1a29e" : "#408A00" }}; height: 100px; width: 100px;border-radius: 50%;box-shadow: -2px 0px 3px 1px #a1a29e;margin: auto' class="text-center">
                    <span class="text-white" style="position: relative;top: 43%;">
                        <i class="fa fa-user fa-3x"></i><br>
                        <small style="font-size: 0.8rem">{{ $equalsNumber }}</small>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection


@section('scripts')
    <script type="application/javascript">
    </script>
@endsection