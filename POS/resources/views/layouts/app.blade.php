<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- 🔧 POLYFILL NATIVEPHP + LIVEWIRE V2 🔧 --}}
    <script>
    (function() {
        'use strict';
        function injectDispatch(obj) {
            if (obj && typeof obj.dispatch !== 'function') {
                obj.dispatch = function(event, detail) {
                    if (window.livewire && typeof window.livewire.emit === 'function') {
                        return window.livewire.emit(event, detail);
                    }
                    if (!window.__lwQueue) window.__lwQueue = [];
                    window.__lwQueue.push({ event: event, detail: detail });
                };
            }
            return obj;
        }
        var _lw = injectDispatch({});
        try {
            Object.defineProperty(window, 'Livewire', {
                get: function() { return _lw; },
                set: function(newVal) { _lw = injectDispatch(newVal || {}); },
                configurable: true, enumerable: true
            });
        } catch(e) { window.Livewire = _lw; }
        document.addEventListener('livewire:load', function() {
            if (window.__lwQueue && window.livewire) {
                window.__lwQueue.forEach(function(item) { window.livewire.emit(item.event, item.detail); });
                window.__lwQueue = [];
            }
        });
    })();
    </script>
    {{-- 🔧 FIN POLYFILL 🔧 --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>{{ config('app.name', 'Refaccionaria Madax') }}</title>

    <!-- Fonts -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="flex flex-col min-h-screen bg-gray-900">
    <div class="flex flex-col flex-grow">
        <!-- Page Heading  en caso de que este logeado-->
        @if(request()->routeIs('dashboard') || request()->routeIs('tablero') || request()->routeIs('clientes'))
        <!-- no incluir el header -->
        @else
        @include('layouts/navigation')
        @endif
        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        @if(request()->routeIs('dashboard') || request()->routeIs('tablero') || request()->routeIs('clientes'))
        <!-- no incluir el footer -->
        @else
        <footer class="bg-gray-900 text-white py-6 px-6 md:px-8">
            <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
                <p>&copy; 2024 Autopartes Madax. Todos los derechos reservados.</p>
                <nav class="flex items-center space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-gray-700">Terminos de servicio</a>
                    <a href="#" class="hover:text-gray-700">Politicas de privacidad</a>
                    <a href="#" class="hover:text-gray-700">Contactanos</a>
                </nav>
            </div>
        </footer>
        <script>
            var botmanWidget = {
                aboutText: 'Autopartes Madax Bot',
                introMessage: '¡Hola! Soy el asistente virtual de Autopartes Madax. ¿En qué puedo ayudarte?',
                title: 'Asistente Virtual',
                mainColor: '#111827', // Color que coincide con tu tema actual
                bubbleBackground: 'green',
                headerTextColor: '#fff',
            };
        </script>
        <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
        @endif
    </div>
    @livewireScripts

    <script>
        // --- BARCODE SCANNER LISTENER (KEYBOARD WEDGE) ---
        (function() {
            let buffer = "";
            let lastKeyTime = Date.now();
            const SCANNER_SPEED_THRESHOLD_MS = 50; // Umbral de velocidad para detectar escáner
            
            document.addEventListener('keydown', function(e) {
                const currentTime = Date.now();
                const timeDiff = currentTime - lastKeyTime;
                lastKeyTime = currentTime;

                // Si detectamos "Enter", evaluamos el buffer
                if (e.key === 'Enter') {
                    if (buffer.length > 2) { // Asumimos que un código de barras real tiene al menos 3 caracteres
                        // Si el buffer se llenó rápidamente, es muy probable que sea un escáner
                        // OJO: Si el usuario escribe rápido pero consistente, podría confundirse, 
                        // pero los escáneres son <20ms por caracter consistentemente.
                        // Para robustez, asumimos que si atrapamos el Enter y hay buffer, intentamos procesar.
                        
                        processBarcode(buffer);
                        buffer = ""; // Limpiar buffer
                        return; // Dejamos que el Enter original ocurra o lo prevenimos según sea necesario
                    }
                    buffer = ""; // Reset si fue un enter manual sin buffer rápido
                } 
                else if (e.key.length === 1) { 
                    // Solo caracteres imprimibles
                    // Si el tiempo entre teclas es mayor al umbral, reseteamos el buffer (es humano escribiendo lento)
                    if (timeDiff > SCANNER_SPEED_THRESHOLD_MS) {
                        buffer = "";
                    }
                    buffer += e.key;
                }
            });

            function processBarcode(code) {
                console.log("Barcode detected:", code);
                const path = window.location.pathname;
                let targetInput = null;

                // 1. EDICIÓN DE COTIZACIONES
                // URL: /dashboard/cotizaciones/X/editar
                if (path.includes('/dashboard/cotizaciones/') && path.includes('/editar')) {
                    // Buscamos el input de Livewire model="buscarClave"
                    // Livewire suele dejar trazas, pero buscaremos por un selector genérico o ID si existe.
                    // En tu vista blade: wire:model.debounce.300ms="buscarClave"
                    // Intenta encontrar por atributo wire:model, o un placeholder específico
                    targetInput = document.querySelector('input[wire\\:model*="buscarClave"]') || 
                                  document.querySelector('input[placeholder*="Buscar clave"]');
                }

                // 2. CREAR VENTA (POS)
                // URL: /dashboard/ventas/create
                else if (path.includes('/dashboard/ventas/create')) {
                    // Mismo selector que cotizaciones probablemente
                     targetInput = document.querySelector('input[wire\\:model*="buscarClave"]') || 
                                   document.querySelector('input[placeholder*="Buscar clave"]');
                }

                // 3. CREAR PRODUCTO
                // URL: /dashboard/productos/create
                else if (path.includes('/dashboard/productos/create')) {
                    targetInput = document.querySelector('input[name="clave"]');
                }

                // 4. DASHBOARD (INVENTARIO)
                // URL: /dashboard
                else if (path === '/dashboard') {
                    // DataTables search input
                    targetInput = document.querySelector('input[type="search"]'); // DataTables default
                }
                
                // --- EJECUTAR ACCIÓN ---
                if (targetInput) {
                    
                    // Si el foco NO estaba ya en el input, prevenimos efectos secundarios y enfocamos
                    // Si ya estaba en el input, probablemente el input ya recibió las teclas nativamente,
                    // PERO el escáner manda Enter al final, lo cual podría enviar el formulario.
                    // Aquí aseguramos que el valor esté correcto y disparamos eventos.
                    
                    targetInput.focus();
                    
                    // Sobrescribimos o insertamos? 
                    // Normalmente escáner reemplaza o llena campo vacío.
                    targetInput.value = code;

                    // DISPARAR EVENTOS PARA LIVEWIRE / JS NATIVO
                    targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    // Opcional: Si es un buscador livewire que requiere "Enter" para buscar inmediatamente:
                    // targetInput.dispatchEvent(new KeyboardEvent('keydown', {'key': 'Enter'}));
                    
                    console.log("Injected barcode into:", targetInput);
                } else {
                    console.warn("No target input found for this route.");
                }
            }
        })();
    </script>
</body>

</html>