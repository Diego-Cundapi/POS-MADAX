
// POLYFILL para compatibilidad NativePHP + Livewire v2
// NativePHP espera Livewire v3 y llama a 'window.Livewire.dispatch'
// Este script redirige esas llamadas a 'window.livewire.emit' (v2)

(function() {
    console.log('Cargando Polyfill de NativePHP...');
    
    // Asegurar que exista el objeto global Livewire (UpperCamelCase)
    if (!window.Livewire) {
        window.Livewire = window.livewire || {};
    }

    // Si no existe 'dispatch', lo creamos
    if (!window.Livewire.dispatch) {
        window.Livewire.dispatch = function(event, detail) {
            console.log('Polyfill dispatch:', event, detail);
            
            // Intentar usar Livewire v2 (objeto global 'livewire')
            if (window.livewire && typeof window.livewire.emit === 'function') {
                return window.livewire.emit(event, detail);
            }
            
            // Si el objeto livewire aún no está listo, lo reintentamos en un momento
            // (Esto pasa si el Polyfill carga antes que Livewire)
            setTimeout(() => {
                if (window.livewire && typeof window.livewire.emit === 'function') {
                    window.livewire.emit(event, detail);
                } else {
                    console.warn('No se pudo redirigir el evento dispatch porque window.livewire no está definido.');
                }
            }, 100);
        };
    }
    
    // Mapeo inverso por si acaso: window.livewire -> window.Livewire
    if (window.livewire && !window.Livewire) {
        window.Livewire = window.livewire;
    }
})();
