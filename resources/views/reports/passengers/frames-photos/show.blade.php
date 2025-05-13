@php
    $countFrames = $files->count();
    $single = $countFrames === 1;
@endphp

<!-- Export button container -->

<div class="row justify-content-center">
    <div class="row ">
        <div class="form-group-button d-flex justify-content-start" style="width: 100%; margin-bottom: 20px;">
            <form action="{{ route('files.export') }}" method="GET">
                <input type="hidden" name="vehicle-report" value="{{ request('vehicle-report') }}">
                <input type="hidden" name="date-report" value="{{ request('date-report') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> Exportar
                </button>
            </form>
        </div>
    </div>
    @forelse($files as $dispatchId => $groupedFiles)
        @php
            $dispatch = $dispatches[$dispatchId] ?? null;
            $routeName = $dispatch && isset($dispatch->route) ? $dispatch->route->name : 'Sin ruta';
        @endphp

        <div class="{{ $single ? 'col-lg-12' : 'col-md-6' }}">
            <div class="card mb-4 shadow-sm border-primary dispatch-card">
                <div class="card-body fs-5">
                    @if($dispatch)
                        <div class="row mb-3 text-center ">
                            <div class="col-md-2">
                                <strong>Ruta:</strong><br>{{ $dispatch->route->name ?? 'Sin ruta' }}
                            </div>

                            <div class="col-md-2">
                                <strong>Fecha salida:</strong><br>{{ $dispatch->date }}
                            </div>
                            <div class="col-md-3">
                                <strong>Hora de salida:</strong><br>{{ $dispatch->departure_time }}
                            </div>
                            <div class="col-md-2">
                                <strong>Fecha Llegada:</strong><br>{{ $dispatch->date_end ?? '---' }}
                            </div>

                            <div class="col-md-3">
                                <strong>Hora llegada:</strong><br>{{ $dispatch->arrival_time }}
                            </div>
                        </div>
                        <hr>
                    @endif

                    <div class="row">
                        @foreach($groupedFiles as $index => $file)
                            <div class="col-md-3 mb-2">
                                <i class="fa fa-file-image-o text-secondary me-1"></i>
                                {{ $file->file_name ?? '-' }}
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-warning col-12">
            <i class="fa fa-exclamation-circle"></i> @lang('No se encontraron resultados')
        </div>
    @endforelse
</div>
<style>
    .form-group-button {
        text-align: center !important;
        width: 100% !important;
        display: flex !important;
        justify-content: flex-start !important; /* alinea a la izquierda */
        margin-bottom: 20px !important;
        padding-left: 50px;
    }
</style>