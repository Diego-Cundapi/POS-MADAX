<?php

namespace App\Http\Livewire;

use App\Models\Producto;
use Livewire\Component;

class Dashboard extends Component
{

    // IMPORTANTE: Esto le dice a Livewire que use los estilos de Bootstrap (AdminLTE)
    // en lugar de los de Tailwind por defecto para la paginación.
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

public function render()
    {
        // CAMBIO CRUCIAL: Usamos get() en lugar de paginate()
        // Esto envía TODOS los productos a la vista para que DataTables los maneje.
        $productos = Producto::orderByDesc('updated_at')
            ->with('categoria')
            ->get(); 

        return view('livewire.dashboard', ['productos' => $productos]);
    }
}
