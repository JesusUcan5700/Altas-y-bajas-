/**
 * Script de Diagnóstico para Validación de Duplicados
 * Ejecutar en la consola del navegador para verificar el estado del sistema
 */

function diagnosticoValidacion() {
    console.log('=== DIAGNÓSTICO DE VALIDACIÓN DE DUPLICADOS ===\n');
    
    // 1. Verificar jQuery
    console.log('1. jQuery:');
    if (typeof jQuery !== 'undefined') {
        console.log('   ✓ jQuery cargado (versión: ' + jQuery.fn.jquery + ')');
    } else {
        console.log('   ✗ jQuery NO cargado');
        return;
    }
    
    // 2. Verificar SweetAlert2
    console.log('\n2. SweetAlert2:');
    if (typeof Swal !== 'undefined') {
        console.log('   ✓ SweetAlert2 cargado');
        
        // Prueba rápida
        try {
            Swal.fire({
                title: 'Prueba de SweetAlert2',
                text: '¡SweetAlert2 está funcionando!',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            console.log('   ✓ Modal de prueba mostrado');
        } catch (e) {
            console.log('   ✗ Error al mostrar modal:', e);
        }
    } else {
        console.log('   ✗ SweetAlert2 NO cargado');
    }
    
    // 3. Verificar función de inicialización
    console.log('\n3. Función inicializarValidacionDuplicados:');
    if (typeof inicializarValidacionDuplicados === 'function') {
        console.log('   ✓ Función encontrada');
    } else {
        console.log('   ✗ Función NO encontrada');
        console.log('   Intentando cargar manualmente...');
        return;
    }
    
    // 4. Verificar variables globales
    console.log('\n4. Variables globales:');
    console.log('   modeloActual:', typeof modeloActual !== 'undefined' ? modeloActual : 'NO DEFINIDO');
    console.log('   idActual:', typeof idActual !== 'undefined' ? idActual : 'NO DEFINIDO');
    
    // 5. Verificar campos del formulario
    console.log('\n5. Campos del formulario:');
    const camposSerie = $('input[name*="NUM_SERIE"]');
    console.log('   Campos de serie encontrados:', camposSerie.length);
    camposSerie.each(function(i) {
        console.log('     Campo ' + (i+1) + ':', {
            id: $(this).attr('id'),
            name: $(this).attr('name'),
            value: $(this).val()
        });
    });
    
    const camposInventario = $('input[name*="NUM_INVENTARIO"]');
    console.log('   Campos de inventario encontrados:', camposInventario.length);
    camposInventario.each(function(i) {
        console.log('     Campo ' + (i+1) + ':', {
            id: $(this).attr('id'),
            name: $(this).attr('name'),
            value: $(this).val()
        });
    });
    
    // 6. Verificar eventos registrados
    console.log('\n6. Eventos registrados:');
    if (camposSerie.length > 0) {
        const eventos = $._data(camposSerie[0], 'events');
        console.log('   Eventos en campo de serie:', eventos ? Object.keys(eventos) : 'Sin eventos');
    }
    if (camposInventario.length > 0) {
        const eventos = $._data(camposInventario[0], 'events');
        console.log('   Eventos en campo de inventario:', eventos ? Object.keys(eventos) : 'Sin eventos');
    }
    
    // 7. Verificar CSRF Token
    console.log('\n7. CSRF Token:');
    let csrfToken = '';
    if (typeof yii !== 'undefined' && yii.getCsrfToken) {
        csrfToken = yii.getCsrfToken();
        console.log('   ✓ Token desde Yii:', csrfToken.substring(0, 20) + '...');
    } else {
        csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            console.log('   ✓ Token desde meta tag:', csrfToken.substring(0, 20) + '...');
        } else {
            console.log('   ✗ Token NO encontrado');
        }
    }
    
    // 8. Prueba de endpoint AJAX
    console.log('\n8. Probando endpoint AJAX...');
    $.ajax({
        url: '/altas_bajas/frontend/web/index.php?r=site/verificar-duplicado',
        method: 'POST',
        data: {
            tipo: 'serie',
            valor: 'TEST_DIAGNOSTICO_12345',
            modelo: 'Equipo',
            _csrf: csrfToken
        },
        success: function(response) {
            console.log('   ✓ Endpoint respondió correctamente');
            console.log('   Respuesta:', response);
        },
        error: function(xhr, status, error) {
            console.log('   ✗ Error en endpoint');
            console.log('   Status:', status);
            console.log('   Error:', error);
            console.log('   Respuesta:', xhr.responseText);
        }
    });
    
    console.log('\n=== FIN DEL DIAGNÓSTICO ===');
    console.log('Revisa los resultados arriba para identificar problemas.\n');
}

// Función para probar manualmente la validación
function probarValidacionManual(tipo, valor) {
    console.log('Probando validación manual...');
    console.log('Tipo:', tipo);
    console.log('Valor:', valor);
    
    if (typeof tipo === 'undefined' || typeof valor === 'undefined') {
        console.error('Uso: probarValidacionManual("serie", "1210802025") o probarValidacionManual("inventario", "1210802025")');
        return;
    }
    
    const campo = tipo === 'serie' 
        ? $('input[name*="NUM_SERIE"]').first()
        : $('input[name*="NUM_INVENTARIO"]').first();
    
    if (campo.length === 0) {
        console.error('Campo no encontrado');
        return;
    }
    
    // Establecer valor
    campo.val(valor);
    console.log('Valor establecido en el campo');
    
    // Ejecutar validación
    if (typeof validarDuplicado === 'function') {
        console.log('Ejecutando validarDuplicado...');
        validarDuplicado(campo, tipo === 'serie' ? 'serie' : 'inventario');
    } else {
        console.error('Función validarDuplicado no encontrada');
    }
}

console.log('Scripts de diagnóstico cargados.');
console.log('Ejecuta: diagnosticoValidacion() para verificar el sistema');
console.log('Ejecuta: probarValidacionManual("serie", "1210802025") para probar la validación');
