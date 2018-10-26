<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Script {{ strtoupper($gps) }}</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <hr>
    <h1 class="display-4 text-capitalize"><i class="fas fa-terminal"></i> PCW Script <strong>{{ $gps }}</strong></h1>
    <p class="lead">
        <span>
            <i class="fa fa-calendar"></i> Updated at: April 2018
        </span>
        <span class="float-right">
            <a href="{{ route('tools-scripts', ['gps' => 'skypatrol']) }}" class="badge badge-light">
                <i class="fa fa-link"></i> Script Skypatrol
            </a>
            <a href="{{ route('tools-scripts', ['gps' => 'coban']) }}" class="badge badge-light">
                <i class="fa fa-link"></i> Script Coban
            </a>
        </span>
    </p>
    <hr class="my-4">
    <button class="btn btn-copy btn-sm btn-info pull-right tooltips" style="float: right;right: 160px;margin-top: 20px;:;position: absolute"
            title="@lang('Copy')" data-clipboard-text="{{ $scriptText }}">
        <i class="fa fa-copy"></i> Copiar
    </button>
    <pre class="pre" style="background: #00222f;color: lightgray !important;overflow-y: auto;height: 400px;padding: 40px">{{ $scriptText }}</pre>
</div>

</body>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>

<script>
    $(document).ready(function () {
        var clipboard = new Clipboard('.btn-copy');

        clipboard.on('success', function (e) {
            alert("@lang('Text copied'):" + e.text);
            e.clearSelection();
        });
    });
</script>

</html>