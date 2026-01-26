<?php

namespace App\Http\Controllers\API\Rocket;

use App\Http\Controllers\Controller;
use App\Models\Apps\Rocket\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PhotoUrlController extends Controller
{
    /**
     * Actualiza el conteo 5G V2 para un registro de despacho específico.
     * Recibe: id_registro, count_5g_v2
     */
    public function updateCount5gV2(Request $request)
    {
        try {
            $idRegistro = $request->input('id_registro');
            $count5gV2 = $request->input('count_5g_v2');

            if (!$idRegistro || !isset($count5gV2)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros requeridos: id_registro, count_5g_v2'
                ], 400);
            }

            // Actualizar registrodespacho
            $updated = DB::table('registrodespacho')
                ->where('id_registro', $idRegistro)
                ->update([
                    'count_5g_v2' => $count5gV2,
                    'ignore_trigger' => true
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => "Registro $idRegistro actualizado correctamente con count_5g_v2 = $count5gV2"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "No se encontró el registro $idRegistro o no hubo cambios"
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error actualizando registro: ' . $e->getMessage()
            ], 500);
        }
    }

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
                    now()->addDays(7) // 7 días de validez (límite máximo para Signature v4)
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
