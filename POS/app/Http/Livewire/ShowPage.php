<?php 
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Categories;
use App\Models\Producto;

class ShowPage extends Component
{
    use WithPagination;

    public $category = '';
    public $search = '';
    public $producto;

    public function mount($producto = null)
    {
        if ($producto) {
            $this->producto = Producto::find($producto);
        }
    }

    public function filterByCategory($category)
    {
        $this->category = $category;
    }

    public function updatingSearch(){
        $this->resetPage();
    }

    public function render()
    {
        if ($this->producto) {
            return view('livewire.mostrar', ['productoMostrar' => $this->producto]);
        }

        // Evitar error si no hay tablas al inicio
        $categories = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
            $categories = Categories::orderBy('name', 'asc')->get();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('productos')) {
            $productos = Producto::query();

            if ($this->search) {
                $productos->where('nombre', 'like', "%{$this->search}%");
            }

            if ($this->category) {
                $productos->where('categories_id', $this->category);
            }

            $productos = $productos->with('categoria')->latest()->paginate(6);
        } else {
            $productos = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 6);
        }

        return view('livewire.show-page', [
            'categories' => $categories,
            'productos' => $productos
        ]);
    }
    public function crearAdmin()
    {
        // 1. FORZAR MIGRACIÓN (Si la tabla no existe, la crea ahora mismo)
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

        // Doble verificación Server-Side
        if (\App\Models\User::count() > 0) {
            return;
        }

        // Crear permisos y roles mínimos si no existen (por seguridad)
        $roleAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        // Permisos básicos
        $perms = ['ver_inventario', 'gestionar_ventas', 'gestionar_cotizaciones', 'ver_crm', 'gestionar_usuarios'];
        foreach ($perms as $perm) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }
        $roleAdmin->syncPermissions(\Spatie\Permission\Models\Permission::all());

        // Crear usuario
        $user = \App\Models\User::create([
            'name' => 'prueba',
            'email' => 'i@prueba.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        $user->assignRole($roleAdmin);

        // Login automático y redirigir
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('dashboard');
    }
}
