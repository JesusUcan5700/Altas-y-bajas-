/**
 * Sistema de Confirmación para Formularios de Edición
 * Requiere confirmación del usuario antes de guardar cambios
 */
(function() {
    'use strict';

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔄 Inicializando sistema de confirmaciones...');
        setupFormConfirmations();
    });

    function setupFormConfirmations() {
        // Buscar todos los formularios en la página
        const forms = document.querySelectorAll('form');
        
        forms.forEach(function(form) {
            // Buscar botones de submit en cada formulario
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            
            submitButtons.forEach(function(button) {
                // Agregar evento de confirmación a cada botón
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    showConfirmation(form, button);
                });
            });
        });
        
        console.log('✅ Sistema de confirmaciones configurado');
    }

    function showConfirmation(form, button) {
        // Determinar el tipo de equipo basado en la URL
        const url = window.location.pathname;
        let title = '¿Confirmar cambios?';
        let message = '¿Está seguro que desea guardar los cambios realizados?';

        // Personalizar mensaje según el tipo de equipo
        if (url.includes('equipo')) {
            title = '¿Actualizar equipo?';
            message = '¿Confirma que desea guardar los cambios en la información del equipo?';
        } else if (url.includes('almacenamiento')) {
            title = '¿Actualizar dispositivo?';
            message = '¿Confirma que desea guardar los cambios en el dispositivo de almacenamiento?';
        } else if (url.includes('videovigilancia')) {
            title = '¿Actualizar cámara?';
            message = '¿Confirma que desea guardar los cambios en la cámara de videovigilancia?';
        } else if (url.includes('telefonia')) {
            title = '¿Actualizar teléfono?';
            message = '¿Confirma que desea guardar los cambios en el equipo de telefonía?';
        } else if (url.includes('ram')) {
            title = '¿Actualizar memoria RAM?';
            message = '¿Confirma que desea guardar los cambios en el módulo de memoria RAM?';
        } else if (url.includes('sonido')) {
            title = '¿Actualizar equipo de sonido?';
            message = '¿Confirma que desea guardar los cambios en el equipo de sonido?';
        } else if (url.includes('nobreak')) {
            title = '¿Actualizar No Break?';
            message = '¿Confirma que desea guardar los cambios en el No Break/UPS?';
        } else if (url.includes('procesador')) {
            title = '¿Actualizar procesador?';
            message = '¿Confirma que desea guardar los cambios en el procesador?';
        } else if (url.includes('monitor')) {
            title = '¿Actualizar monitor?';
            message = '¿Confirma que desea guardar los cambios en el monitor?';
        }

        // Mostrar confirmación usando SweetAlert2
        Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: '<i class="fas fa-save me-2"></i>Sí, guardar',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
            focusCancel: true,
            allowEscapeKey: true,
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) {
                console.log('✅ Usuario confirmó guardar');
                // Cambiar texto del botón a "Guardando..."
                changeButtonToLoading(button);
                // Enviar el formulario
                form.submit();
            } else {
                console.log('❌ Usuario canceló');
            }
        });
    }

    function changeButtonToLoading(button) {
        // Guardar el texto original
        const originalText = button.innerHTML || button.value;
        button.setAttribute('data-original-text', originalText);
        
        // Cambiar a estado de carga
        if (button.tagName === 'BUTTON') {
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        } else {
            button.value = 'Guardando...';
        }
        button.disabled = true;
    }

})();
