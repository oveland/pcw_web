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
            // Acepta tanto vehicle_id (legacy) como vehicle_number + company_id (nuevo)
            $vehicleId = $request->get('vehicle_id');
            $vehicleNumber = $request->get('vehicle_number');
            $companyId = $request->get('company_id');
            $start = $request->get('start');
            $end   = $request->get('end');

            if (!$start || !$end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros requeridos: start, end',
                ], 400);
            }

            // Si no hay vehicle_id directo, intentar buscarlo por número
            if (!$vehicleId) {
                if (!$vehicleNumber || !$companyId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Se requiere vehicle_id O (vehicle_number y company_id)',
                    ], 400);
                }

                $vehicle = DB::table('vehicles')
                    ->where('number', $vehicleNumber)
                    ->where('company_id', $companyId)
                    ->select('id')
                    ->first();

                if (!$vehicle) {
                    return response()->json([
                        'success' => false,
                        'message' => "Vehículo $vehicleNumber de la compañía $companyId no encontrado",
                    ], 404);
                }
                $vehicleId = $vehicle->id;
            }

            // Buscar las fotos del vehículo, cámara E, en el rango
            $photos = Photo::query()
                ->where('vehicle_id', $vehicleId)
                ->where('side', 'E') // solo cámara E
                ->whereBetween('date', [$start, $end])
                ->orderBy('date')
                ->get(['id', 'vehicle_id', 'path', 'date']);

            if ($photos->isEmpty()) {
                // Debug info para el usuario
                $totalPhotos = Photo::where('vehicle_id', $vehicleId)
                    ->whereBetween('date', [$start, $end])
                    ->count();

                return response()->json([
                    'success' => true,
                    'count'   => 0,
                    'debug_info' => [
                        'vehicle_id' => $vehicleId,
                        'side_searched' => 'E',
                        'total_photos_in_range' => $totalPhotos,
                        'message' => 'No se encontraron fotos side=E en este rango, pero existen ' . $totalPhotos . ' fotos totales (de otros lados).'
                    ],
                    'urls'    => [],
                ]);
            }

            // Generar URLs firmadas válidas por 7 días
            $urls = $photos->map(function ($photo) {
                // Muy importante: usar el valor crudo de BD
                $key = ltrim($photo->getRawOriginal('path'), '/');

                $url = Storage::disk('s3')->temporaryUrl(
                    $key,
                    now()->addDays(7) // 7 días de validez (límite máximo para Signature v4)
                );

                return [
                    'id'   => $photo->id,
                    'date' => $photo->date,
                    'camera' => $photo->side, // Nombre 'camera' para mayor claridad
                    'url'  => $url,
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
                'message' => 'Error obteniendo URLs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna las URLs firmadas (solo cámara T) buscando el vehículo por número y compañía.
     */
    public function getTUrls(Request $request)
    {
        try {
            $vehicleNumber = $request->get('vehicle_number');
            $companyId = $request->get('company_id');
            $start = $request->get('start');
            $end   = $request->get('end');

            if (!$vehicleNumber || !$companyId || !$start || !$end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parámetros requeridos: vehicle_number, company_id, start, end',
                ], 400);
            }

            // Buscar el ID del vehículo usando número y compañía
            $vehicle = DB::table('vehicles')
                ->where('number', $vehicleNumber)
                ->where('company_id', $companyId)
                ->select('id')
                ->first();

            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => "Vehículo número $vehicleNumber de la compañía $companyId no encontrado",
                ], 404);
            }

            $vehicleId = $vehicle->id;

            // Buscar las fotos del vehículo, cámara T (todo lo que no sea E), en el rango
            $photos = Photo::query()
                ->where('vehicle_id', $vehicleId)
                ->where('side', '!=', 'E') // Todo lo que no sea E se considera T (cámaras numeradas)
                ->whereBetween('date', [$start, $end])
                ->orderBy('date')
                ->get(['id', 'vehicle_id', 'path', 'date']);

            if ($photos->isEmpty()) {
                // Debug info para el usuario
                $totalPhotos = Photo::where('vehicle_id', $vehicleId)
                    ->whereBetween('date', [$start, $end])
                    ->count();

                return response()->json([
                    'success' => true,
                    'count'   => 0,
                    'debug_info' => [
                        'vehicle_id' => $vehicleId,
                        'side_searched' => 'NOT E (T/Numbered)',
                        'total_photos_in_range' => $totalPhotos,
                        'message' => 'No se encontraron fotos (side != E) en este rango. Total fotos encontradas: ' . $totalPhotos
                    ],
                    'urls'    => [],
                ]);
            }

            // Generar URLs firmadas válidas por 7 días
            $urls = $photos->map(function ($photo) {
                $key = ltrim($photo->getRawOriginal('path'), '/');

                $url = Storage::disk('s3')->temporaryUrl(
                    $key,
                    now()->addDays(7)
                );

                return [
                    'id'   => $photo->id,
                    'date' => $photo->date,
                    'camera' => $photo->side, // Nombre 'camera' para mayor claridad
                    'url'  => $url,
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
                'message' => 'Error obteniendo URLs T: ' . $e->getMessage()
            ], 500);
        }
    }
}
