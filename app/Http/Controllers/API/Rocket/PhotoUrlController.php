<?php

namespace App\Http\Controllers\API\Rocket;

use App\Http\Controllers\Controller;
use App\Models\Apps\Rocket\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoUrlController extends Controller
{
    /**
     * Retorna las URLs firmadas (solo cámara E) por vehículo y rango de fecha/hora.
     */
    public function getUrls(Request $request)
    {
        try {
            $vehicleId = $request->get('vehicle_id');
            $start = $request->get('start');
            $end   = $request->get('end');

            if (!$vehicleId || !$start || !$end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros insuficientes: vehicle_id, start, end son requeridos',
                ], 400);
            }

            // Buscar las fotos del vehículo, cámara E, en el rango
            $photos = Photo::query()
                ->where('vehicle_id', $vehicleId)
                ->where('side', 'E') // solo cámara E
                ->whereBetween('date', [$start, $end])
                ->orderBy('date')
                ->get(['id', 'vehicle_id', 'path', 'date']);

            if ($photos->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'count'   => 0,
                    'urls'    => [],
                ]);
            }

            // Generar URLs firmadas válidas por 1 hora
            $urls = $photos->map(function ($photo) {
                // Muy importante: usar el valor crudo de BD
                $key = ltrim($photo->getRawOriginal('path'), '/');

                $url = Storage::disk('s3')->temporaryUrl(
                    $key,
                    now()->addHour() // 1 hora de validez
                );

                // Si solo quieres la URL, dejamos solo esto
                return [
                    'url'  => $url,
                    // si en el futuro quieres más info, aquí puedes agregar:
                    // 'id' => $photo->id,
                    // 'date' => $photo->date,
                ];
            });

            return response()->json([
                'success' => true,
                'count'   => $urls->count(),
                'urls'    => $urls,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
