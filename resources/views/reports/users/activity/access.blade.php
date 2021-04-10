@if( Auth::user()->isAdmin() )
    @if( count($logs) > 0 )
        <style>
            table thead tr th{
                padding: 10px;
                text-transform: uppercase;
                font-size: 120%;
                border-bottom: solid 2px #d6d6d6;
            }
            table {
                width: 100%;
                font-size: small;
                text-align: start;
                display: table;
                border-collapse: collapse;
                border-color: grey;
            }
            tr:nth-child(even) {
                background: #f5f5f5
            }
            tr:nth-child(odd) {
                background: #FFF
            }

            body {
                font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                font-size: 14px;
                line-height: 1.42857143;
                color: #333;
                background-color: #fff;
            }
            html {
                font-size: 10px;
                -webkit-tap-highlight-color: rgba(0,0,0,0);
            }

            html {
                font-family: sans-serif;
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
            }
        </style>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Fecha</th>
                <th>Hora</th>
            </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user->usuario??'SIN ASIGNAR' }}</td>
                    <td>{{ $log->user->nombre??'SIN ASIGNAR' }}</td>
                    <td>{{ $log->date }}</td>
                    <td>{{ explode('.',$log->time)[0] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <h1>@lang('No access log has been saved on '){{ $date }}</h1>
    @endif
@else
    <h1>@lang('Access denied. Report visible for users admin only!'){{ $date }}</h1>
@endif