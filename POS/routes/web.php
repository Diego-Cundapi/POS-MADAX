<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\BotManController;
use App\Http\Livewire\ShowPage;
use App\Http\Livewire\Tablero;
// Importaciones de Cotizaciones
use App\Http\Controllers\CotizacionController;
use App\Http\Livewire\Admin\Cotizaciones\CrearCotizacion;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ActivationController;

// --- RUTAS DE ACTIVACIÓN (Sin verificación - siempre accesibles) ---
Route::get('/activate', [ActivationController::class, 'show'])->name('app.activate');
Route::post('/activate', [ActivationController::class, 'activate'])->name('app.activate.submit');

// --- VERIFICACIÓN DE ACTIVACIÓN ANTES DE TODAS LAS RUTAS ---
// Esta función se ejecuta en cada petición web
Route::middleware([\App\Http\Middleware\CheckAppActivation::class])->group(function () {

// --- RUTA RAÍZ ---
Route::get('/', \App\Http\Livewire\ShowPage::class)->name('index');

// --- CHATBOT ---
Route::match(['get', 'post'], '/botman', [BotManController::class, 'handle']);

// --- RUTAS PÚBLICAS / CLIENTE ---
Route::get('/productos/{producto?}', ShowPage::class)->name('producto');
Route::get('/compras', [ComprasController::class, 'index'])->name('vercompras');
Route::get('/compras/{detalle}', [ComprasController::class, 'detalle'])->name('detallecompra');

// --- CARRITO DE COMPRAS (Requiere Auth) ---
Route::group(['middleware' => ['auth']], function () {
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/agregarproducto', [CarritoController::class, 'agregarProducto'])->name('carrito.agregarproducto');
    Route::get('/incrementar/{id}', [CarritoController::class, 'incrementarProducto'])->name('carrito.incrementarproducto');
    Route::get('/decrementar/{id}', [CarritoController::class, 'decrementarProducto'])->name('carrito.decrementarproducto');
    Route::get('/eliminaritem/{id}', [CarritoController::class, 'eliminarItem'])->name('carrito.eliminaritem');
    Route::get('/eliminarcarrito', [CarritoController::class, 'eliminarCarrito'])->name('carrito.eliminarcarrito');
    Route::get('/confirmarrcarrito', [CarritoController::class, 'confirmarCarrito'])->name('carrito.confirmarcarrito');


});

// --- PERFIL DE USUARIO ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ==================================================================================
//  ZONA ADMINISTRATIVA (PROTEGIDA POR ROLES)
// ==================================================================================

// 1. Rutas accesibles por ADMIN y EMPLEADO (Operativa Diaria)
Route::middleware(['auth', 'role:Admin|Empleado'])->group(function () {
    
    // Dashboard General -> Inventario (Vista home de admin)
    Route::get('/dashboard', \App\Http\Livewire\Dashboard::class)->name('dashboard');

    // Categorías
    Route::resource('/dashboard/categoria', CategoriaController::class)->names('categoria');

    // Productos e Importación
    Route::resource('/dashboard/productos', ProductosController::class)->except('show')->names('productos');
    Route::post('/dashboard/productos/importar', [ProductosController::class, 'importarExcel'])->name('productos.importar');

    // Ventas (Pedidos)
    Route::get('/dashboard/ventas/create', \App\Http\Livewire\Admin\Ventas\CrearVenta::class)->name('ventas.create');
    Route::resource('/dashboard/ventas', PedidoController::class)->except(['create', 'store', 'show'])->names('ventas');
    Route::get('/pedidos/{id}/pdf', [PedidoController::class, 'descargarPDF'])->name('ventas.pdf');

    // Clientes
    Route::get('/dashboard/clientes', \App\Http\Livewire\Clientes::class)->name('clientes');
    
    // Tickets (Universal)
    Route::get('/ticket/{tipo}/{id}', [\App\Http\Controllers\TicketController::class, 'imprimir'])->name('ticket.imprimir');

    // Cotizaciones
    Route::get('/dashboard/cotizaciones/crear', CrearCotizacion::class)->name('cotizaciones.create');
    Route::get('/dashboard/cotizaciones/{cotizacionId}/editar', CrearCotizacion::class)->name('cotizaciones.edit');
    Route::get('/cotizaciones/{id}/pdf', [CotizacionController::class, 'pdf'])->name('cotizaciones.pdf');
    Route::post('/cotizaciones/{id}/convertir', [CotizacionController::class, 'convertirVenta'])->name('cotizaciones.convertir');
    Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizaciones.store');
    Route::resource('/dashboard/cotizaciones', CotizacionController::class)
        ->except(['create', 'store', 'edit'])
        ->names('cotizaciones');
        
    Route::get('/busqueda-global', [SearchController::class, 'index'])->name('global.search');
});

// 2. Rutas EXCLUSIVAS de ADMIN (Gerencia y Configuración)
Route::middleware(['auth', 'role:Admin'])->group(function () {
    
    // Tablero CRM (Estadísticas Financieras)
    Route::get('/dashboard/tablero', \App\Http\Livewire\Tablero::class)->name('tablero');

    // Exportar Inventario (Solo Admin)
    Route::get('/dashboard/productos/exportar', [\App\Http\Controllers\ProductosController::class, 'exportarExcel'])->name('productos.exportar');

    // Gestión de Usuarios (CRUD)
    Route::post('/dashboard/usuarios/factory-reset', [\App\Http\Controllers\UserController::class, 'factoryReset'])->name('usuarios.reset');
    Route::resource('/dashboard/usuarios', \App\Http\Controllers\UserController::class)->names('usuarios');
});

}); // Cierre del grupo de activación

require __DIR__ . '/auth.php';
