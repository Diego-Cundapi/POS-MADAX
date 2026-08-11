<?php

return [
    'hot_reload' => [
        base_path('app/Providers/NativeAppServiceProvider.php'),
    ],

    /**
     * La version se compara contra la ultima version migrada guardada en el
     * equipo del cliente; si no coincide, la app reintenta las migraciones
     * pendientes al arrancar. Sube este numero en cada release.
     */
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.3'),
];
