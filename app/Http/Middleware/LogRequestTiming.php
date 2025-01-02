<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRequestTiming
{
    /**
     * Manejar una solicitud entrante.
     *F
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        Log::info('Middleware LogRequestTiming ejecutado.');
//        // Registrar el tiempo de inicio
//        $startTime = microtime(true);
//
//        // Ejecutar la solicitud
//        $response = $next($request);
//
//        // Calcular el tiempo que tomó la solicitud
//        $duration = microtime(true) - $startTime;
//
//        // Registrar los detalles de la solicitud y el tiempo de respuesta
//        Log::info('Solicitud a la API:', [
//            'url' => $request->fullUrl(),
//            'method' => $request->method(),
//            'params' => $request->all(),
//            'ip' => $request->ip(),
//            'user_agent' => $request->header('User-Agent'),
//            'duration' => $duration . ' segundos',
//            'status_code' => $response->getStatusCode(),
//        ]);
//
        return 0;
    }
}
