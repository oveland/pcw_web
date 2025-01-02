<!DOCTYPE html>
<html >
<head>
    <title>Alerta: Despachos sin fotos</title>
</head>
<body>
<h2>Se han detectado despachos sin fotos</h2>
<p>La siguiente lista muestra los despachos sin fotos para la empresa:</p>

<table border="1" cellspacing="0" cellpadding="8">
    <thead>
    <tr>
        <th>ID de Registro</th>
        <th>Vehículo</th>
        <th>Ruta</th>
        <th>Fecha</th>
        <th>Hora de Salida</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($despachos as $despacho)
        @php
        //dd($despacho);
        @endphp
        <tr style="text-align: center">
            <td> {{$despacho['id_registro'] }} </td>
            <td> {{$despacho['vehicle_number'] }} </td>
            <td> {{$despacho['routeName'] }} </td>
            <td> {{$despacho['date'] }} </td>
            <td> {{$despacho['departure_time'] }} </td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>Por favor, revisa los vehículos mencionados.</p>
</body>
</html>
