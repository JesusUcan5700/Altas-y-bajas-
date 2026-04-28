/**
 * Validación de Duplicados - Debug Version
 */

console.log('🔴 [VALIDACION] Script cargado correctamente');

let modeloActual = '';
let idActual = '';
let urlVerificarDuplicado = '/site/verificar-duplicado';

// Función principal de inicialización
function inicializarValidacionDuplicados(modelo, id = '') {
    console.log('🔵 [VALIDACION] Inicializando con modelo:', modelo, 'ID:', id);

    modeloActual = modelo;
    idActual = id;

    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', configurarValidadores);
    } else {
        console.log('🟢 [VALIDACION] DOM ya cargado, configurando validadores');
        setTimeout(configurarValidadores, 500);
    }
}

function configurarValidadores() {
    console.log('🔧 [VALIDACION] Configurando validadores...');

    // Buscar campos de SERIE
    const camposSerie = document.querySelectorAll('input[name*="NUM_SERIE"], input[name*="NUMERO_SERIE"]');
    console.log('📍 [VALIDACION] Campos de serie encontrados:', camposSerie.length);

    camposSerie.forEach((campo, idx) => {
        console.log(`  [${idx}] Campo serie: ${campo.name} (ID: ${campo.id})`);
        campo.addEventListener('blur', function() {
            const valor = this.value.trim();
            console.log('👁️ [BLUR] Evento blur en serie:', valor);
            if (valor.length > 0) {
                validarDuplicado(this, 'serie', valor);
            }
        });
    });

    // Buscar campos de INVENTARIO
    const camposInventario = document.querySelectorAll('input[name*="NUM_INVENTARIO"], input[name*="NUMERO_INVENTARIO"]');
    console.log('📍 [VALIDACION] Campos de inventario encontrados:', camposInventario.length);

    camposInventario.forEach((campo, idx) => {
        console.log(`  [${idx}] Campo inventario: ${campo.name} (ID: ${campo.id})`);
        campo.addEventListener('blur', function() {
            const valor = this.value.trim();
            console.log('👁️ [BLUR] Evento blur en inventario:', valor);
            if (valor.length > 0) {
                validarDuplicado(this, 'inventario', valor);
            }
        });
    });

    // Configurar validación al enviar
    const form = document.querySelector('form');
    if (form) {
        console.log('✅ [VALIDACION] Formulario encontrado, agregando validación de envío');
        form.addEventListener('submit', validarAlEnviar);
    }
}

// Función para validar duplicados
function validarDuplicado(inputElement, tipo, valor) {
    console.log('🔍 [VALIDACION] Validando', tipo, ':', valor);

    // Obtener CSRF token
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    console.log('🔐 [VALIDACION] CSRF Token:', csrfToken ? 'OK' : 'NO ENCONTRADO');

    // URL
    const url = (typeof urlVerificarDuplicado !== 'undefined') ? urlVerificarDuplicado : '/site/verificar-duplicado';
    console.log('🌐 [VALIDACION] URL:', url);

    // Preparar datos
    const formData = new FormData();
    formData.append('tipo', tipo);
    formData.append('valor', valor);
    formData.append('modelo', modeloActual);
    formData.append('id', idActual);
    formData.append('_csrf-frontend', csrfToken);

    console.log('📤 [VALIDACION] Enviando solicitud...');

    // Hacer petición
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('📡 [RESPUESTA] HTTP:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('📊 [DATOS] Respuesta recibida:', data);

        if (data.existe) {
            console.log('🚨 [ALERTA] ¡DUPLICADO DETECTADO!');

            // Marcar como inválido
            inputElement.classList.add('is-invalid');
            inputElement.classList.remove('is-valid');
            inputElement.setAttribute('data-duplicado', 'true');

            // MOSTRAR POPUP
            mostrarPopup(tipo, valor, data);
        } else {
            console.log('✅ [OK] Número válido');
            inputElement.classList.remove('is-invalid');
            inputElement.classList.add('is-valid');
            inputElement.removeAttribute('data-duplicado');
        }
    })
    .catch(error => {
        console.error('❌ [ERROR] Error de fetch:', error);
        alert('Error al validar: ' + error.message);
    });
}

// Mostrar popup
function mostrarPopup(tipo, valor, data) {
    console.log('🎯 [POPUP] Intentando mostrar popup...');
    console.log('🎯 [POPUP] SweetAlert disponible:', typeof Swal !== 'undefined');

    // Si SweetAlert está disponible, usarlo
    if (typeof Swal !== 'undefined') {
        console.log('🎯 [POPUP] Usando SweetAlert');

        Swal.fire({
            icon: 'warning',
            title: '⚠️ ¡NÚMERO DUPLICADO!',
            html: `
                <div style="text-align: left; padding: 2rem 0;">
                    <!-- HEADER CON ICONO -->
                    <div style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 8px 24px rgba(255, 152, 0, 0.3);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.4rem; font-weight: bold;">Este ${tipo === 'serie' ? 'número de serie' : 'número de inventario'} ya está en uso</h3>
                        <p style="margin: 0; font-size: 1rem; opacity: 0.95;">No puede utilizarse nuevamente en el sistema</p>
                    </div>

                    <!-- INFORMACIÓN DEL NÚMERO -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 2px solid #ffc107;">
                        <p style="margin: 0 0 0.75rem 0; color: #666; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                            <strong>${tipo === 'serie' ? '📋 Número de Serie' : '📊 Número de Inventario'}</strong>
                        </p>
                        <div style="background: white; padding: 1.25rem; border-radius: 8px; text-align: center; font-weight: bold; color: #d63384; font-size: 1.3rem; letter-spacing: 2px; border: 2px solid #ffc107; font-family: 'Courier New', monospace;">
                            ${valor}
                        </div>
                    </div>

                    <!-- INFORMACIÓN DEL EQUIPO REGISTRADO -->
                    <div style="background: #e3f2fd; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 5px solid #0d6efd;">
                        <p style="margin: 0 0 0.75rem 0; color: #0d6efd; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                            <strong>🏢 Registrado En</strong>
                        </p>
                        <div style="background: white; padding: 1rem; border-radius: 6px; color: #0d6efd; font-weight: 600; font-size: 1.1rem;">
                            ${data.dispositivo || 'N/A'}
                        </div>
                    </div>

                    <!-- DETALLES ADICIONALES -->
                    ${data.detalles?.marca || data.detalles?.modelo || data.detalles?.estado ? `
                        <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <p style="margin: 0 0 1rem 0; color: #495057; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                                <strong>📋 Detalles del Equipo</strong>
                            </p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                ${data.detalles?.marca ? `
                                    <div style="background: white; padding: 0.75rem; border-radius: 6px; border-left: 3px solid #6c757d;">
                                        <p style="margin: 0 0 0.3rem 0; color: #6c757d; font-size: 0.8rem; font-weight: 600;">MARCA</p>
                                        <p style="margin: 0; color: #212529; font-weight: 600;">${data.detalles.marca}</p>
                                    </div>
                                ` : ''}
                                ${data.detalles?.modelo ? `
                                    <div style="background: white; padding: 0.75rem; border-radius: 6px; border-left: 3px solid #6c757d;">
                                        <p style="margin: 0 0 0.3rem 0; color: #6c757d; font-size: 0.8rem; font-weight: 600;">MODELO</p>
                                        <p style="margin: 0; color: #212529; font-weight: 600;">${data.detalles.modelo}</p>
                                    </div>
                                ` : ''}
                                ${data.detalles?.estado ? `
                                    <div style="background: white; padding: 0.75rem; border-radius: 6px; border-left: 3px solid #6c757d;">
                                        <p style="margin: 0 0 0.3rem 0; color: #6c757d; font-size: 0.8rem; font-weight: 600;">ESTADO</p>
                                        <p style="margin: 0; color: #212529; font-weight: 600;">${data.detalles.estado}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    ` : ''}

                    <!-- MENSAJE FINAL -->
                    <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%); padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ff9800; text-align: center;">
                        <p style="margin: 0; color: #664d03; font-size: 1rem; font-weight: 500;">
                            <i class="fas fa-lightbulb" style="margin-right: 0.5rem;"></i>
                            <strong>Por favor, ingresa un número diferente y válido</strong>
                        </p>
                    </div>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#ff9800',
            allowOutsideClick: false,
            didOpen: (modal) => {
                modal.classList.add('swal-pulse');
            },
            width: '700px',
            padding: '2rem'
        });
    } else {
        // Fallback a alert si SweetAlert no está disponible
        console.log('🎯 [POPUP] SweetAlert NO disponible, usando alert()');
        alert('⚠️ NÚMERO DUPLICADO\n\n' + valor + '\n\nRegistrado en: ' + (data.dispositivo || 'N/A') + '\n\nPor favor usa un número diferente');
    }
}

// Validación al enviar el formulario
function validarAlEnviar(event) {
    const camposConDuplicados = document.querySelectorAll('[data-duplicado="true"]');

    if (camposConDuplicados.length > 0) {
        event.preventDefault();
        event.stopPropagation();

        let detalles = '';
        camposConDuplicados.forEach(campo => {
            const tipo = campo.name.includes('SERIE') ? 'SERIE' : 'INVENTARIO';
            detalles += `<li style="margin: 0.75rem 0; font-size: 1rem;"><strong>${tipo}:</strong> <code style="background: white; padding: 0.5rem 0.75rem; border-radius: 4px; font-weight: bold; color: #dc3545; border: 2px solid #dc3545;">${campo.value}</code></li>`;
        });

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: '❌ ¡NO SE PUEDE GUARDAR!',
                html: `
                    <div style="text-align: left; padding: 2rem 0;">
                        <!-- HEADER CON ICONO -->
                        <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 8px 24px rgba(220, 53, 69, 0.3);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🛑</div>
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.4rem; font-weight: bold;">Números duplicados detectados</h3>
                            <p style="margin: 0; font-size: 1rem; opacity: 0.95;">Los siguientes números ya están en uso y no se pueden guardar</p>
                        </div>

                        <!-- LISTA DE DUPLICADOS -->
                        <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 2px solid #dc3545;">
                            <p style="margin: 0 0 1rem 0; color: #dc3545; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                                <strong>📋 Números Duplicados:</strong>
                            </p>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                ${detalles}
                            </ul>
                        </div>

                        <!-- ACCIONES REQUERIDAS -->
                        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); padding: 1.5rem; border-radius: 8px; border-left: 4px solid #dc3545; text-align: center;">
                            <p style="margin: 0; color: #721c24; font-size: 1rem; font-weight: 600;">
                                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                                <strong>Reemplaza los números duplicados con valores únicos antes de guardar</strong>
                            </p>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false,
                didOpen: (modal) => {
                    modal.classList.add('swal-pulse');
                },
                width: '700px',
                padding: '2rem'
            });
        } else {
            alert('⚠️ ¡DATOS DUPLICADOS DETECTADOS!\n\nLos siguientes números ya están registrados:\n' +
                  Array.from(camposConDuplicados).map(c => '- ' + c.value).join('\n') +
                  '\n\nPor favor, utiliza números únicos.');
        }

        return false;
    }
}

console.log('🟢 [VALIDACION] Script listo para inicializar');
