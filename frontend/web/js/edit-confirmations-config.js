/**
 * Configuraciones específicas para confirmaciones de edición
 * Este archivo se carga después de confirm-save.js para personalizar comportamientos específicos
 */

document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que ConfirmSave esté disponible
    if (typeof ConfirmSave !== 'undefined') {
        initCustomConfigurations();
    } else {
        // Intentar nuevamente después de un segundo
        setTimeout(function() {
            if (typeof ConfirmSave !== 'undefined') {
                initCustomConfigurations();
            }
        }, 1000);
    }
});

function initCustomConfigurations() {
    // Detectar el tipo de página actual
    const currentUrl = window.location.pathname;
    
    // Configuración específica para equipos de cómputo
    if (currentUrl.includes('equipo/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar equipo de cómputo?',
            text: 'Se guardarán todos los cambios realizados en la información del equipo, incluyendo componentes adicionales como discos duros y memoria RAM.',
            confirmButtonText: '<i class="fas fa-desktop me-2"></i>Actualizar Equipo',
            html: `
                <div class="text-start">
                    <p>Se actualizará la siguiente información:</p>
                    <ul class="list-unstyled ms-3">
                        <li><i class="fas fa-microchip text-primary me-2"></i>Especificaciones técnicas</li>
                        <li><i class="fas fa-hdd text-info me-2"></i>Discos duros y almacenamiento</li>
                        <li><i class="fas fa-memory text-warning me-2"></i>Memoria RAM</li>
                        <li><i class="fas fa-map-marker-alt text-success me-2"></i>Ubicación y detalles</li>
                    </ul>
                    <p class="text-muted"><small><i class="fas fa-info-circle me-1"></i>Esta acción se registrará en el historial de auditoría.</small></p>
                </div>
            `
        });
    }
    
    // Configuración específica para dispositivos de almacenamiento
    else if (currentUrl.includes('almacenamiento/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar dispositivo de almacenamiento?',
            text: 'Se guardarán los cambios en la información del dispositivo de almacenamiento.',
            confirmButtonText: '<i class="fas fa-hdd me-2"></i>Actualizar Dispositivo',
            html: `
                <div class="text-start">
                    <p>Se actualizará:</p>
                    <ul class="list-unstyled ms-3">
                        <li><i class="fas fa-tag text-primary me-2"></i>Marca y modelo</li>
                        <li><i class="fas fa-info text-info me-2"></i>Tipo y especificaciones</li>
                        <li><i class="fas fa-map-marker-alt text-success me-2"></i>Ubicación</li>
                        <li><i class="fas fa-clipboard text-warning me-2"></i>Estado y observaciones</li>
                    </ul>
                </div>
            `
        });
    }
    
    // Configuración específica para videovigilancia
    else if (currentUrl.includes('videovigilancia/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar cámara de videovigilancia?',
            text: 'Se guardarán los cambios en la configuración de la cámara de videovigilancia.',
            confirmButtonText: '<i class="fas fa-video me-2"></i>Actualizar Cámara',
            confirmButtonColor: '#dc3545', // Rojo como el botón original
            html: `
                <div class="text-start">
                    <p>Se actualizará:</p>
                    <ul class="list-unstyled ms-3">
                        <li><i class="fas fa-video text-danger me-2"></i>Información de la cámara</li>
                        <li><i class="fas fa-network-wired text-info me-2"></i>Configuración de red</li>
                        <li><i class="fas fa-map-marker-alt text-success me-2"></i>Ubicación y zona</li>
                        <li><i class="fas fa-cog text-warning me-2"></i>Configuraciones técnicas</li>
                    </ul>
                </div>
            `
        });
    }
    
    // Configuración específica para telefonía
    else if (currentUrl.includes('telefonia/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar equipo de telefonía?',
            text: 'Se guardarán los cambios en la información del equipo telefónico.',
            confirmButtonText: '<i class="fas fa-phone me-2"></i>Actualizar Teléfono'
        });
    }
    
    // Configuración específica para memoria RAM
    else if (currentUrl.includes('ram/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar módulo de memoria RAM?',
            text: 'Se guardarán los cambios en la información del módulo de memoria RAM.',
            confirmButtonText: '<i class="fas fa-memory me-2"></i>Actualizar RAM',
            html: `
                <div class="text-start">
                    <p><i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <strong>Importante:</strong> Verifique que las especificaciones técnicas sean correctas.</p>
                    <p class="text-muted"><small>Los cambios afectarán el inventario y historial del componente.</small></p>
                </div>
            `
        });
    }
    
    // Configuración específica para equipos de sonido
    else if (currentUrl.includes('sonido/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar equipo de sonido?',
            text: 'Se guardarán los cambios en la información del equipo de sonido.',
            confirmButtonText: '<i class="fas fa-volume-up me-2"></i>Actualizar Equipo'
        });
    }
    
    // Configuración específica para No Break/UPS
    else if (currentUrl.includes('nobreak/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar No Break/UPS?',
            text: 'Se guardarán los cambios en la información del sistema de alimentación ininterrumpida.',
            confirmButtonText: '<i class="fas fa-battery-three-quarters me-2"></i>Actualizar No Break',
            confirmButtonColor: '#dc3545', // Rojo como el botón original
            html: `
                <div class="text-start">
                    <p><i class="fas fa-info-circle text-info me-2"></i>
                    Se actualizará la información del UPS/No Break incluyendo capacidad, ubicación y estado.</p>
                    <p class="text-warning"><small><i class="fas fa-exclamation-triangle me-1"></i>
                    Asegúrese de que la información de capacidad sea correcta para el mantenimiento preventivo.</small></p>
                </div>
            `
        });
    }
    
    // Configuración específica para procesadores
    else if (currentUrl.includes('procesador/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar procesador?',
            text: 'Se guardarán los cambios en la información del procesador.',
            confirmButtonText: '<i class="fas fa-microchip me-2"></i>Actualizar Procesador',
            html: `
                <div class="text-start">
                    <p><i class="fas fa-microchip text-primary me-2"></i>
                    Se actualizará la información técnica del procesador.</p>
                    <p class="text-muted"><small>Verifique que las especificaciones sean correctas.</small></p>
                </div>
            `
        });
    }
    
    // Configuración específica para monitores
    else if (currentUrl.includes('monitor/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar monitor?',
            text: 'Se guardarán los cambios en la información del monitor.',
            confirmButtonText: '<i class="fas fa-desktop me-2"></i>Actualizar Monitor',
            html: `
                <div class="text-start">
                    <p><i class="fas fa-desktop text-info me-2"></i>
                    Se actualizará la información del monitor incluyendo especificaciones y ubicación.</p>
                </div>
            `
        });
    }
    
    // Configuración específica para impresoras
    else if (currentUrl.includes('impresora/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar impresora?',
            text: 'Se guardarán los cambios en la información de la impresora.',
            confirmButtonText: '<i class="fas fa-print me-2"></i>Actualizar Impresora'
        });
    }
    
    // Configuración específica para conectividad
    else if (currentUrl.includes('conectividad/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar equipo de conectividad?',
            text: 'Se guardarán los cambios en la información del equipo de conectividad.',
            confirmButtonText: '<i class="fas fa-network-wired me-2"></i>Actualizar Equipo'
        });
    }
    
    // Configuración específica para baterías
    else if (currentUrl.includes('bateria/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar batería?',
            text: 'Se guardarán los cambios en la información de la batería.',
            confirmButtonText: '<i class="fas fa-battery-full me-2"></i>Actualizar Batería'
        });
    }
    
    // Configuración específica para adaptadores
    else if (currentUrl.includes('adaptador/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar adaptador?',
            text: 'Se guardarán los cambios en la información del adaptador.',
            confirmButtonText: '<i class="fas fa-plug me-2"></i>Actualizar Adaptador'
        });
    }
    
    // Configuración específica para micrófonos
    else if (currentUrl.includes('microfono/editar')) {
        ConfirmSave.setCustom('form', {
            title: '¿Actualizar micrófono?',
            text: 'Se guardarán los cambios en la información del micrófono.',
            confirmButtonText: '<i class="fas fa-microphone me-2"></i>Actualizar Micrófono'
        });
    }
}

// Función para mostrar notificación de éxito después del guardado
function showSuccessNotification(type = 'equipo') {
    const messages = {
        'equipo': 'Equipo actualizado correctamente',
        'almacenamiento': 'Dispositivo de almacenamiento actualizado',
        'videovigilancia': 'Cámara de videovigilancia actualizada',
        'telefonia': 'Equipo de telefonía actualizado',
        'ram': 'Módulo de RAM actualizado',
        'sonido': 'Equipo de sonido actualizado',
        'nobreak': 'No Break/UPS actualizado'
    };
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¡Actualizado!',
            text: messages[type] || 'Información actualizada correctamente',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
}

// Manejar respuestas después del envío del formulario
window.addEventListener('beforeunload', function(e) {
    // Verificar si hay formularios con cambios no guardados
    const forms = document.querySelectorAll('form[data-confirm-setup="true"]');
    let hasUnsavedChanges = false;
    
    forms.forEach(form => {
        if (form.checkFormChanges && form.checkFormChanges()) {
            hasUnsavedChanges = true;
        }
    });
    
    if (hasUnsavedChanges) {
        const message = '¿Está seguro de que desea salir? Los cambios no guardados se perderán.';
        e.returnValue = message;
        return message;
    }
});
