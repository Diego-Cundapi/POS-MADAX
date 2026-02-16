<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Mostrar lista de usuarios
    public function index()
    {
        // Obtenemos todos los usuarios excepto el actual para evitar auto-eliminación accidental, o todos.
        $users = User::with('roles')->get(); 
        return view('usuarios.index', compact('users'));
    }

    // Mostrar formulario de crear
    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    // Guardar nuevo usuario (o restaurar uno eliminado previamente)
    public function store(Request $request)
    {
        // Verificar si existe un usuario soft-deleted con ese email
        $trashedUser = User::onlyTrashed()
            ->where('email', strtolower($request->email))
            ->first();

        // Validar: la regla unique excluye soft-deleted (whereNull deleted_at)
        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        if ($trashedUser) {
            // Restaurar el usuario eliminado y actualizar sus datos
            $trashedUser->restore();
            $trashedUser->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
            ]);
            $trashedUser->syncRoles([$request->role]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario restaurado y actualizado correctamente.');
        }

        // Crear usuario nuevo
        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::all();
        return view('usuarios.edit', compact('user', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required'
        ]);

        $user->name = $request->name;
        $user->email = strtolower($request->email);
        
        // Solo actualizamos password si se envía
        if($request->filled('password')){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Sincronizar roles (reemplaza los anteriores)
        $user->syncRoles([$request->role]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    // Eliminar usuario (SoftDelete - solo marca como eliminado)
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('usuarios.index')->with('error', 'Usuario no encontrado.');
        }

        // Prevenir auto-eliminación
        if (auth()->id() == $id) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // SoftDelete - solo establece deleted_at, no elimina físicamente
        $user->delete();
        
        return redirect()->route('usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }

    // Resetear a valores de fábrica
    public function factoryReset()
    {
        // Seguridad doble: Solo permitir al Admin
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'No autorizado');
        }

        // Ejecutar el comando forzando la confirmación
        \Illuminate\Support\Facades\Artisan::call('app:reset-data', ['--force' => true]);

        // Cerrar sesión y redirigir
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        
        return redirect('/login')->with('success', 'El sistema ha sido reseteado a valores de fábrica.');
    }
}
