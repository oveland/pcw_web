<div class="container d-flex justify-content-center mt-4">
    <div class="card shadow-sm p-4 w-100" style="max-width: 1100px;">
        <h2 class="text-primary text-center mb-4">Promedio de Pasajeros</h2>

        <div class="row mb-3 text-center">
            <div class="col-6">
                <p><strong>Desde la hora:</strong> {{ $timeStart }}</p>
            </div>
            <div class="col-6">
                <p><strong>Hasta la hora:</strong> {{ $timeEnd }}</p>
            </div>
        </div>

        <!-- Resaltar el promedio de pasajeros -->
        <div class="alert alert-info d-flex align-items-center justify-content-center text-center p-3">
            <h4 class="fw-bold mb-0">🚍 Promedio: <span class="text-danger">{{ $averagePassengers }}</span></h4>
        </div>

        <!-- Tabla de fechas -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm text-center">
                <thead class="table-dark">
                <tr class="text-center"> <!-- Aquí centramos los encabezados -->
                    <th class="text-center">Fechas Escogidas</th>
                    <th class="text-center">Día</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $daysOfWeek = [
                        'Monday' => 'Lunes',
                        'Tuesday' => 'Martes',
                        'Wednesday' => 'Miércoles',
                        'Thursday' => 'Jueves',
                        'Friday' => 'Viernes',
                        'Saturday' => 'Sábado',
                        'Sunday' => 'Domingo',
                    ];
                @endphp
                @foreach ($dates as $date)
                    <tr>
                        <td>{{ $date }}</td>
                        <td>{{ $daysOfWeek[\Carbon\Carbon::parse($date)->format('l')] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
