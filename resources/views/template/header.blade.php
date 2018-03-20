<meta charset="utf-8"/>
<title>@yield('title','PCW | Servicios GPS')</title>
<link rel="icon" type="image/png" href="{{ asset('pcw.png') }}">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="PCW | Servicios GPS" name="description"/>
<meta content="Oscar Velásquez" name="author"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- ================== BEGIN BASE CSS STYLE ================== -->
<link href="{{asset('http://fonts.googleapis.com/css?family=Nunito:400,300,700')}}" rel="stylesheet" id="fontFamilySrc"/>
<link href="{{asset('assets/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/font-awesome/css/font-awesome-animation.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/simple-line-icons/simple-line-icons.css')}}" rel="stylesheet">
<link href="{{asset('assets/css/animate.min.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/css/style.min.css')}}" rel="stylesheet"/>
<!-- ================== END BASE CSS STYLE ================== -->

<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->
<link href="{{asset('assets/plugins/gritter/css/jquery.gritter.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/toastr/toastr.min.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/bootstrap-select/bootstrap-select.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css')}}" rel="stylesheet"/>
<link href="{{asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css')}}" rel="stylesheet"/>

<link href="{{asset('assets/plugins/bootstrap-eonasdan-datetimepicker/build/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet"/>

<link href="{{asset('css/application.css')}}" rel="stylesheet"/>
<!-- ================== END PAGE LEVEL CSS STYLE ================== -->

<!-- ================== BEGIN BASE JS ================== -->
<script src="{{asset('assets/plugins/pace/pace.min.js')}}"></script>
<!-- ================== END BASE JS ================== -->

<!-- Scripts -->
<script>
    window.Laravel = '{!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!}';
</script>