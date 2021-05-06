@extends('layouts.blank')

@section('stylesheets')
    <style>

    </style>
@endsection

@section('content')
    <div style="opacity: 0">
        <iframe id="ts-check" src="http://alpha.pcwserviciosgps.com/videoIndex" frameborder="0" width="100%"></iframe>
        <form id="tsLogin" name="tsLogin" frameborder="0" action="http://alpha.pcwserviciosgps.com/api/regdc" method="POST">
            <input name="ver" value="1"/>
            <input name="method" value="login"/>
            <input name="account" value="pcwoveland"/>
            <input name="password" value=""/>
            <input name="language" value=""/>
            <input name="validCode" value=""/>
        </form>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        const _ctx = 'http://alpha.pcwserviciosgps.com';
        const urlVideo = _ctx + "/videoIndex"

        function encodeSTR(str) {
            let t = "";
            for (let x = 0; x < str.length; x++) {
                let a = str.charCodeAt(x);
                if (x != 0) {
                    t += '|'
                }
                t += (Number(a)).toString(10)
            }
            return t
        }

        function autoLogin() {
            let tsLogin = $('#tsLogin');
            tsLogin.find('input[name="password"]').val(encodeSTR('Oveland91'));
            console.log('Loggin....')
            document.tsLogin.submit();
        }

        function check() {
            var iframe = document.getElementById('ts-check');
            iframe.onload = function(data) {
                autoLogin();
            }
        }
        // check();
        autoLogin();
    </script>
@endsection