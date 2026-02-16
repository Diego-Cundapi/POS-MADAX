<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CheckAppActivation
{
    /**
     * Código de activación válido (puedes cambiarlo o moverlo a .env)
     */
    private const ACTIVATION_CODE = 'dWZgxV1YN9RicAX';

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si estamos en la ruta de activación, dejar pasar
        if ($request->is('activate') || $request->is('activate/*')) {
            return $next($request);
        }

        // DEBUG: Forzar siempre verificación
        $isActivated = $this->isActivated();
        
        // Debug: Escribir a archivo directamente
        file_put_contents(
            storage_path('logs/activation_debug.log'),
            date('Y-m-d H:i:s') . " | URL: " . $request->path() . " | Activated: " . ($isActivated ? 'YES' : 'NO') . PHP_EOL,
            FILE_APPEND
        );

        if (!$isActivated) {
            return redirect()->route('app.activate');
        }

        return $next($request);
    }

    /**
     * Verifica si la aplicación está activada.
     */
    private function isActivated(): bool
    {
        try {
            // Verificar si la tabla existe
            if (!Schema::hasTable('app_settings')) {
                return false;
            }

            $setting = DB::table('app_settings')
                ->where('key', 'app_activated')
                ->first();

            return $setting && $setting->value === 'true';

        } catch (\Throwable $e) {
            // Si hay algún error con la BD, asumir que no está activada
            return false;
        }
    }

    /**
     * Verifica si un código de activación es válido.
     */
    public static function validateCode(string $code): bool
    {
        return $code === self::ACTIVATION_CODE;
    }

    /**
     * Marca la aplicación como activada.
     */
    public static function activate(): bool
    {
        try {
            DB::table('app_settings')->updateOrInsert(
                ['key' => 'app_activated'],
                ['value' => 'true', 'updated_at' => now(), 'created_at' => now()]
            );
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
