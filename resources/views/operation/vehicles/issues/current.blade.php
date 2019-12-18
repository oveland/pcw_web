@extends('layouts.blank')

@section('stylesheets')
    <style>
        .p-20{
            padding: 10px !important;
        }
        .page-footer{
            display: none;
        }
        body{
            background: transparent;
        }
        .issue-type-description{
            font-size: 0.8em;
            font-weight: bold;
        }
        .label{
            padding: 8px !important;
        }
        .info-vehicle{
            font-size: 1.3em !important;
        }
    </style>
@endsection
@section('content')
    @if(count($currentVehiclesIssues))
        <div class="contaisner p-0">
            <h1 class="page-header">
                <i class="fa fa-car" aria-hidden="true"></i>
                <small>{{ count($currentVehiclesIssues) }} @lang('Vehicle with issues')</small>
                <button onclick="reload()" class="btn btn-xs btn-circle btn-rounded p-0 tooltips" data-title="@lang('Refresh')" data-placement="right">
                    <i class="fa fa-refresh"></i>
                </button>
            </h1>
            <div class="">
                @foreach($currentVehiclesIssues as $issue)
                    @php
                        $type = $issue->type;
                        $driver = $issue->driver;
                        $vehicle = $issue->vehicle;
                    @endphp
                    <span class="p-1">
                        <label class="label label-{{ $type->css_class }} tooltips" data-html="true" data-placement="top" data-title="<i class='fa fa-exclamation-triangle text-{{ $type->css_class }}'></i> <span class=''>{{ $issue->observations }}</span>">
                            <span class="info-vehicle tooltips" data-title="{{ $issue->updated_at }}<br> <small class='issue-type-description'>({{ $type->description }})</small>" data-placement="bottom" data-html="true">
                                <i class="fa fa-car"></i> {{ $vehicle->number }}
                            </span>
                        </label>
                    </span>
                @endforeach
            </div>
        </div>
    @else
        @include('partials.alerts.noVehiclesIssuesFound')
    @endif
@endsection

@section('scripts')
    <script>
        function reload(){
            document.location.reload();
        }
    </script>
@endsection