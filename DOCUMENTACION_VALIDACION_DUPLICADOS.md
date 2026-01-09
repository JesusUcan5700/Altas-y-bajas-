# Documentación - Validación de Duplicados

## Problema Detectado

El usuario reportó que la ventana modal de SweetAlert2 no aparece cuando hay un número duplicado. En su lugar, aparece el mensaje de validación estándar de Yii2.

## Causa

Sweet Alert2 no estaba cargado en las páginas. El script JavaScript `validacion-duplicados.js` requiere que SweetAlert2 esté disponible para mostrar las alertas modales.

## Solución Implementada

### 1. Agregar SweetAlert2 a todos los formularios

En cada archivo de formulario (`agregar.php`), agregar antes del script de validación:

```php
<?php
// Registrar SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => \yii\web\View::POS_HEAD]);

// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJs("inicializarValidacionDuplicados('NombreModelo');", \yii\web\View::POS_READY);
?>
```

### 2. Formularios que necesitan actualización

- [x] Nobreak - Actualizado
- [ ] Equipo
- [ ] Monitor
- [ ] Impresora
- [ ] Adaptador
- [ ] Almacenamiento
- [ ] Microfono

## Cómo Funciona

1. **Usuario escribe** un número de serie o inventario
2. **Al salir del campo** (evento blur), se ejecuta validación AJAX
3. **Backend verifica** en todas las tablas del sistema
4. **Si hay duplicado**:
   - Se marca el campo en rojo
   - Se muestra ventana modal de SweetAlert2
   - Se bloquea el envío del formulario
5. **Si no hay duplicado**:
   - Se marca el campo en verde
   - Permite continuar

## Archivos Involucrados

- **Backend**: `SiteController.php` → `actionVerificarDuplicado()` (línea 5862)
- **Frontend**: `validacion-duplicados.js`
- **Vistas**: Todos los formularios de agregar

## Próximos Pasos

Agregar SweetAlert2 a los formularios restantes.
