<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- 🔧 POLYFILL CRÍTICO PARA NATIVEPHP + LIVEWIRE V2 - DEBE SER LO PRIMERO 🔧 --}}
    <script>
    (function() {
        'use strict';
        // Crear namespace Livewire inmediatamente
        if (!window.Livewire) window.Livewire = {};
        
        // Crear función dispatch ANTES de que NativePHP la llame
        window.Livewire.dispatch = function(event, detail) {
            // Si livewire v2 está listo, redirigir
            if (window.livewire && typeof window.livewire.emit === 'function') {
                return window.livewire.emit(event, detail);
            }
            // Si no está listo, encolar
            if (!window.__lwQueue) window.__lwQueue = [];
            window.__lwQueue.push({ event: event, detail: detail });
        };
        
        // Procesar cola cuando livewire esté listo
        document.addEventListener('livewire:load', function() {
            if (window.__lwQueue && window.livewire) {
                window.__lwQueue.forEach(function(item) {
                    window.livewire.emit(item.event, item.detail);
                });
                window.__lwQueue = [];
            }
        });
        
        console.log('[NativePHP Polyfill] ✓ Listo');
    })();
    </script>
    {{-- 🔧 FIN DEL POLYFILL 🔧 --}}

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- IFrame Preloader Removal Workaround --}}
    <!-- IFrame Preloader Removal Workaround -->
    <style type="text/css">
        body.iframe-mode .preloader {
            display: none !important;
        }
    </style>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets - SIEMPRE cargar los CSS base de AdminLTE --}}
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

    @if(config('adminlte.google_fonts.allowed', true))
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if(config('adminlte.livewire'))
    @if(intval(app()->version()) >= 7)
    @livewireStyles
    @else
    <livewire:styles />
    @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')

    {{-- Favicon --}}
    @if(config('adminlte.use_ico_only'))
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
    <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
    <link rel="manifest" crossorigin="use-credentials" href="{{ asset('favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicons/ms-icon-144x144.png') }}">
    @endif

</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    {{-- Base Scripts - SIEMPRE cargar jQuery y AdminLTE PRIMERO --}}
    {{-- 🔧 FIX: Electron/NativePHP jQuery Global Scope --}}
    <script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>if (window.module) module = window.module;</script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    {{-- Extra Configured Plugins Scripts (DataTables, etc.) --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if(config('adminlte.livewire'))
    @if(intval(app()->version()) >= 7)
    @livewireScripts
    @else
    <livewire:scripts />
    @endif
    @endif

    {{-- Vite Assets (SweetAlert2 local) --}}
    @vite(['resources/js/app.js'])

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

    <script>
        console.log("AdminLTE Barcode Listener Loaded");
        // --- BARCODE SCANNER LISTENER (KEYBOARD WEDGE) ---
        (function() {
            let buffer = "";
            let lastKeyTime = Date.now();
            const SCANNER_SPEED_THRESHOLD_MS = 50; 
            
            document.addEventListener('keypress', function(e) {
                // Usamos 'keypress' en lugar de 'keydown' porque el escáner está usando "Alt Codes"
                // (ej. Alt+55 para '7'). 'keydown' captura '5' y '5'. 'keypress' captura el '7' resultante.
                
                const currentTime = Date.now();
                const timeDiff = currentTime - lastKeyTime;
                lastKeyTime = currentTime;

                // DETECCIÓN DE ENTER
                if (e.key === 'Enter' || e.keyCode === 13) {
                    if (buffer.length > 2) { 
                        e.preventDefault(); 
                        e.stopPropagation();
                        processBarcode(buffer);
                        buffer = ""; 
                        return; 
                    }
                    buffer = ""; 
                } 
                else {
                    // En keypress, casi todo es imprimible. 
                    // Verificamos rapidez para distinguir de escritura manual.
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

                // Definimos selectores
                // 1. COTIZACIONES (Crear y Editar)
                // Nota: El usuario especificó '/dashboard/cotizaciones/crear' y '/editar'
                if (path.includes('/dashboard/cotizaciones/')) { // Cubre /crear y /editar
                     targetInput = document.querySelector('input[wire\\:model*="buscarClave"]') || 
                                   document.querySelector('input[placeholder*="Buscar clave"]');
                }
                
                // 2. CREAR VENTA (POS)
                else if (path.includes('/dashboard/ventas/create')) {
                     targetInput = document.querySelector('input[wire\\:model*="buscarClave"]') || 
                                   document.querySelector('input[placeholder*="Buscar clave"]');
                }
                
                // 3. CREAR PRODUCTO
                else if (path.includes('/dashboard/productos/create')) {
                    targetInput = document.querySelector('input[name="clave"]');
                }
                
                // 4. DASHBOARD GENERAL (Inventario)
                else if (path === '/dashboard' || path === '/dashboard/') {
                    // Prioridad 1: Input específico de la tabla de inventario
                    targetInput = document.querySelector('input[aria-controls="tabla-inventario"]'); 

                    // Prioridad 2: Si no existe, buscamos el search de DataTables genérico, 
                    // PERO EXCLUYENDO el buscador del navbar (name="query")
                    if (!targetInput) {
                        targetInput = document.querySelector('input[type="search"]:not([name="query"])');
                    }
                }
                
                if (targetInput) {
                    targetInput.focus();
                    targetInput.value = code;
                    targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    // DISPARAR ENTER AUTOMÁTICO
                    // Esto es necesario porque tu input tiene wire:keydown.enter="enterClave"
                    targetInput.dispatchEvent(new KeyboardEvent('keydown', {
                        key: 'Enter',
                        code: 'Enter',
                        which: 13,
                        keyCode: 13,
                        bubbles: true,
                        cancelable: true
                    }));
                    
                    console.log("Injected barcode into:", targetInput);
                } else {
                    console.warn("No target input found to inject barcode in this route.");
                }
            }
        })();
    </script>
</body>

</html>