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
                <div style="text-align: left;">
                    <div style="background: #ffc107; color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <h4 style="margin: 0;">Este ${tipo === 'serie' ? 'número de serie' : 'número de inventario'} ya está en uso</h4>
                    </div>

                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <p><strong>${tipo === 'serie' ? 'Número de Serie' : 'Número de Inventario'}:</strong></p>
                        <code style="background: white; padding: 0.5rem; font-size: 1.1rem; font-weight: bold; color: #d63384;">${valor}</code>

                        <p style="margin-top: 1rem;"><strong>Registrado en:</strong></p>
                        <div style="background: white; padding: 0.5rem; border-left: 4px solid #0d6efd; color: #0d6efd;">
                            ${data.dispositivo || 'N/A'}
                        </div>

                        ${data.detalles?.marca ? `<p style="margin: 0.5rem 0 0;"><strong>Marca:</strong> ${data.detalles.marca}</p>` : ''}
                        ${data.detalles?.modelo ? `<p style="margin: 0.25rem 0 0;"><strong>Modelo:</strong> ${data.detalles.modelo}</p>` : ''}
                        ${data.detalles?.estado ? `<p style="margin: 0.25rem 0 0;"><strong>Estado:</strong> ${data.detalles.estado}</p>` : ''}
                    </div>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#ff9800'
        });
    } else {
        // Fallback a alert si SweetAlert no está disponible
        console.log('🎯 [POPUP] SweetAlert NO disponible, usando alert()');
        alert('⚠️ NÚMERO DUPLICADO\n\n' + valor + '\n\nRegistrado en: ' + (data.dispositivo || 'N/A') + '\n\nPor favor usa un número diferente');
    }
}

console.log('🟢 [VALIDACION] Script listo para inicializar');
