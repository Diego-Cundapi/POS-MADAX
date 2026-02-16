<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckAppActivation;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    /**
     * Muestra la página de activación.
     */
    public function show()
    {
        return view('activation.index');
    }

    /**
     * Procesa el código de activación.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'activation_code' => 'required|string',
        ]);

        $code = trim($request->input('activation_code'));

        if (CheckAppActivation::validateCode($code)) {
            // Código válido - activar la app
            if (CheckAppActivation::activate()) {
                return redirect()->route('login')
                    ->with('success', '¡Aplicación activada correctamente! Ahora puedes iniciar sesión.');
            }

            return back()->withErrors(['activation_code' => 'Error al guardar la activación. Intenta de nuevo.']);
        }

        // Código inválido
        return back()
            ->withInput()
            ->withErrors(['activation_code' => 'Código de activación inválido. Por favor verifica e intenta de nuevo.']);
    }
}
