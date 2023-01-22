<!--[if lt IE 9]>
<script src="{{ asset('assets/global/plugins/respond.min.js') }}"></script>
<script src="{{ asset('assets/global/plugins/excanvas.min.js') }}"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/uniform/jquery.uniform.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ asset('assets/global/scripts/app.js') }}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

<!-- BEGIN "GLOBAL"PAGE LEVEL PLUGINS -->
<script src="{{ asset('assets/global/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('assets/plugins/moment/moment-with-locales.js')}}"></script>
<!-- END "GLOBAL"PAGE LEVEL PLUGINS -->

<template id="loading">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center"><i class="fa fa-spinner fa-pulse fa-fw"></i></div>
    </div>
</template>

<template id="select-loading">
    <option value=""><i class="fa fa-spinner fa-pulse fa-fw"></i> @lang('Loading...')</option>
</template>

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js')}}"></script>
<script src="{{asset('assets/plugins/bootstrap-eonasdan-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/plugins/gritter/js/jquery.gritter.js')}}"></script>

<script src="{{asset('assets/plugins/bootstrap-select/bootstrap-select.min.js')}}"></script>
<script src="{{asset('assets/plugins/select2/dist/js/select2.min.js')}}"></script>
{{--<script src="{{asset('assets/js/apps.min.js')}}"></script>--}}
<script src="{{asset('js/application.js')}}"></script>
<script src="{{asset('js/jquery.validate.oiva.js')}}"></script>
<!-- ================== END PAGE LEVEL JS ================== -->

<script src="{{ asset('js/main.js') }}" type="text/javascript"></script>

@include('partials.scripts.general')
@include('partials.alerts.reports.passengers.issuesByVehiclesScript')

@yield('templateScripts')
@yield('scripts')