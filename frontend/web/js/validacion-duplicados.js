/**
 * Validación de Duplicados para Números de Serie e Inventario
 * Muestra alertas modales con SweetAlert2 cuando se detectan duplicados
 */

// Variable global para almacenar el modelo actual
let modeloActual = '';
let idActual = '';

/**
 * Inicializa la validación de duplicados para un modelo específico
 * @param {string} modelo - Nombre del modelo (ej: 'Nobreak', 'Equipo', etc.)
 * @param {string} id - ID del registro (opcional, para edición)
 */
function inicializarValidacionDuplicados(modelo, id = '') {
    modeloActual = modelo;
    idActual = id;

    console.log('[Validación Duplicados] Inicializando para modelo:', modelo);

    // Buscar campos de número de serie (todas las variaciones)
    const camposSerie = $('input[name*="NUMERO_SERIE"], input[name*="numero_serie"], input[name*="NUM_SERIE"], input[name*="num_serie"]');
    console.log('[Validación Duplicados] Campos de serie encontrados:', camposSerie.length, camposSerie);
    
    camposSerie.each(function() {
        const campo = $(this);
        console.log('[Validación Duplicados] Campo serie - ID:', campo.attr('id'), 'Name:', campo.attr('name'));
        
        // Agregar validación
        campo.on('blur', function () {
            console.log('[Validación Duplicados] Blur en campo de serie');
            validarDuplicado($(this), 'serie');
        });
    });

    // Buscar campos de número de inventario (todas las variaciones)
    const camposInventario = $('input[name*="NUMERO_INVENTARIO"], input[name*="numero_inventario"], input[name*="NUM_INVENTARIO"], input[name*="num_inventario"]');
    console.log('[Validación Duplicados] Campos de inventario encontrados:', camposInventario.length, camposInventario);
    
    camposInventario.each(function() {
        const campo = $(this);
        console.log('[Validación Duplicados] Campo inventario - ID:', campo.attr('id'), 'Name:', campo.attr('name'));
        
        // Agregar validación
        campo.on('blur', function () {
            console.log('[Validación Duplicados] Blur en campo de inventario');
            validarDuplicado($(this), 'inventario');
        });
    });

    // Prevenir envío del formulario si hay duplicados
    $('form').on('beforeSubmit', function (e) {
        const form = $(this);

        // Verificar si hay campos marcados como inválidos
        if (form.find('.is-invalid[data-duplicado="true"]').length > 0) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: '¡Atención!',
                text: 'Hay números duplicados en el formulario. Por favor corrígelos antes de continuar.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#d33'
            });

            return false;
        }
    });
    
    console.log('[Validación Duplicados] Inicialización completada');
}

/**
 * Valida si un número está duplicado en el sistema
 * @param {jQuery} input - Campo de entrada a validar
 * @param {string} tipo - 'serie' o 'inventario'
 */
function validarDuplicado(input, tipo) {
    const valor = input.val().trim();
    
    console.log('[Validación Duplicados] Validando', tipo, ':', valor);

    // No validar si el campo está vacío
    if (!valor) {
        input.removeClass('is-invalid is-valid');
        input.removeAttr('data-duplicado');
        console.log('[Validación Duplicados] Campo vacío, no se valida');
        return;
    }

    // Mostrar indicador de carga
    input.addClass('validating');
    console.log('[Validación Duplicados] Enviando petición AJAX...');
    
    // Obtener token CSRF de Yii o de la meta tag
    let csrfToken = '';
    if (typeof yii !== 'undefined' && yii.getCsrfToken) {
        csrfToken = yii.getCsrfToken();
    } else {
        csrfToken = $('meta[name="csrf-token"]').attr('content');
    }
    
    console.log('[Validación Duplicados] CSRF Token:', csrfToken ? 'OK' : 'NO ENCONTRADO');

    $.ajax({
        url: '/altas_bajas/frontend/web/index.php?r=site/verificar-duplicado',
        method: 'POST',
        data: {
            tipo: tipo,
            valor: valor,
            modelo: modeloActual,
            id: idActual,
            _csrf: csrfToken
        },
        success: function (response) {
            input.removeClass('validating');
            console.log('[Validación Duplicados] Respuesta recibida:', response);

            if (response.existe) {
                console.log('[Validación Duplicados] DUPLICADO ENCONTRADO!');
                
                // Marcar como inválido
                input.addClass('is-invalid');
                input.removeClass('is-valid');
                input.attr('data-duplicado', 'true');

                // Mostrar alerta modal con SweetAlert2
                mostrarAlertaDuplicado(tipo, valor, response.dispositivo);

            } else {
                console.log('[Validación Duplicados] No hay duplicado, campo válido');
                
                // Marcar como válido
                input.removeClass('is-invalid');
                input.addClass('is-valid');
                input.removeAttr('data-duplicado');
            }
        },
        error: function (xhr, status, error) {
            input.removeClass('validating');
            console.error('[Validación Duplicados] Error en AJAX:', status, error);
        ole.log('[Validación Duplicados] Mostrando modal de duplicado');
    console.log('[Validación Duplicados] SweetAlert2 disponible:', typeof Swal !== 'undefined');
    
    const tipoCampo = tipo === 'serie' ? 'Número de Serie' : 'Número de Inventario';

    if (typeof Swal === 'undefined') {
        console.error('[Validación Duplicados] SweetAlert2 no está cargado!');
        alert(`¡${tipoCampo} Duplicado!\n\n${tipoCampo}: ${valor}\n\nEste número ya está registrado en:\n${dispositivo}\n\nPor favor, ingresa un número diferente.`);
        return;
    }

    Swal.fire({
        icon: 'warning',
        title: '¡Número Duplicado!',
        html: `
            <div class="text-start">
                <p class="mb-2"><strong>${tipoCampo}:</strong> <code>${valor}</code></p>
                <p class="mb-2">Este número ya está registrado en:</p>
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${dispositivo}</strong>
                </div>
                <p class="mt-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Por favor, ingresa un número diferente para continuar.
                </p>
            </div>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#d33',
        customClass: {
            popup: 'swal-wide',
            htmlContainer: 'text-start'
        },
        didOpen: () => {
            console.log('[Validación Duplicados] Modal abierto');="mt-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Por favor, ingresa un número diferente para continuar.
                </p>
            </div>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#d33',
        customClass: {
            popup: 'swal-wide',
            htmlContainer: 'text-start'
        },
        didOpen: () => {
            // Enfocar el campo después de cerrar la alerta
            Swal.getConfirmButton().addEventListener('click', function () {
                setTimeout(() => {
                    $('input.is-invalid[data-duplicado="true"]').first().focus().select();
                }, 100);
            });
        }
    });
}

// Estilos CSS para el indicador de carga
const style = document.createElement('style');
style.textContent = `
    .validating {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20'%3E%3Ccircle cx='10' cy='10' r='8' fill='none' stroke='%230d6efd' stroke-width='2'%3E%3Canimate attributeName='stroke-dasharray' values='0 50;50 0' dur='1s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 20px 20px;
    }
    
    .swal-wide {
        width: 600px !important;
    }
    
    .swal2-html-container code {
        background: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        color: #d63384;
        font-family: 'Courier New', monospace;
    }
`;
document.head.appendChild(style);
