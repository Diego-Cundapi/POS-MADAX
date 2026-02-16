import './bootstrap';

// --- BORRA O COMENTA ESTAS LÍNEAS DE ALPINE ---
// import Alpine from 'alpinejs';
// window.Alpine = Alpine;
// Alpine.start();  <--- ESTA LÍNEA ES LA QUE ESTABA MATANDO TU APP
// -----------------------------------------------

// SweetAlert está bien, déjalo así
import Swal from 'sweetalert2';
window.Swal = Swal;

/**
 * --- FIX PARA E.EXE (NATIVEPHP/ELECTRON) ---
 * Soluciona el problema donde los inputs dejan de responder después de:
 * 1. Abrir/Cerrar una alerta nativa (alert/confirm).
 * 2. Descargar un archivo (PDF/Excel).
 * 3. Imprimir un ticket (window.open).
 */

// 1. Al recuperar el foco en la ventana principal, asegura que el documento esté activo
window.addEventListener('focus', () => {
    if (document.activeElement === document.body) {
        // Un pequeño timeout ayuda a que Electron termine de procesar el cambio de ventana
        setTimeout(() => {
            window.focus();
        }, 100);
    }
});

// 2. Listener agresivo para recuperar foco al hacer clic en cualquier lado
document.addEventListener('click', () => {
    // Si por alguna razón el sistema operativo piensa que no tenemos foco
    if (!document.hasFocus()) {
        window.focus();
    }
}, true); // UseCapture: Se ejecuta antes que otros eventos

// 3. Sobrescribir window.alert y confirm para usar SweetAlert (Opcional, pero recomendado)
// Esto evita que se abran diálogos nativos que bloquean el proceso de renderizado
window.nativeAlert = window.alert;
window.alert = function(message) {
    Swal.fire({
        title: 'Atención',
        text: message,
        confirmButtonColor: '#3085d6',
    });
};

window.nativeConfirm = window.confirm;
window.confirm = function(message) {
    return Swal.fire({
        title: 'Confirmar',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        return result.isConfirmed;
    });
};

// Función helper para confirmaciones en onclick
window.confirmAction = function(event, message, callback) {
    // Prevenir el envío automático del formulario
    event.preventDefault();
    
    Swal.fire({
        title: 'Confirmar',
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
};