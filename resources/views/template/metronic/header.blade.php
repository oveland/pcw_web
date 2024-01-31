<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="https://www.linkedin.com/in/oveland/" name="author"/>
<meta content="{{ config('app.name', 'PCW | Servicios GPS') }}" name="description"/>
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>PCW | {{ __(ucfirst($current)) }}</title>

<!-- BEGIN GLOBAL MANDATORY STYLES -->

<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<link href="{{ asset('fonts/Google_Open_Sand.css') }}" rel="stylesheet" type="text/css"/>

<link href="{{asset('assets/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet"/>
<link href="{{asset('https://cdn.jsdelivr.net/npm/font-awesome-animation@1.1.1/css/font-awesome-animation.min.css')}}" rel="stylesheet">

<link href="{{asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/global/plugins/uniform/css/uniform.default.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->

<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN THEME GLOBAL STYLES -->
<link href="{{asset('assets/global/css/components-md.min.css')}}" rel="stylesheet" id="style_components" type="text/css"/>
<link href="{{asset('assets/global/css/plugins-md.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- END THEME GLOBAL STYLES -->

<!-- BEGIN THEME LAYOUT STYLES -->
<link href="{{asset('assets/layouts/layout/css/layout.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{asset('assets/layouts/layout/css/themes/darkblue.min.css')}}" rel="stylesheet" type="text/css" id="style_color"/>
<link href="{{asset('assets/layouts/layout/css/custom.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}"/>

<!-- BEGIN "GLOBAL" PAGE LEVEL PLUGINS -->
<link href="{{asset('assets/global/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END "GLOBAL" PAGE LEVEL PLUGINS -->

<!-- ================== BEGIN BASE CSS STYLE ================== -->

<link href="{{asset('assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/simple-line-icons/simple-line-icons.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/animate.min.css')}}" rel="stylesheet"/>

<!-- ================== END BASE CSS STYLE ================== -->


<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->
<link href="{{asset('assets/plugins/gritter/css/jquery.gritter.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/bootstrap-eonasdan-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"/>

<!-- ================== END PAGE LEVEL CSS STYLE ================== -->

<!-- Styles -->
<link href="{{ asset('css/application.css') }}" rel="stylesheet">

<!-- ================== BEGIN BASE JS ================== -->
{{--<script src="{{asset('assets/plugins/pace/pace.min.js')}}"></script>--}}
<!-- ================== END BASE JS ================== -->


<script>
    window.Laravel = '{!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!}';
</script>

@yield('stylesheets')
@yield('templateStyles')