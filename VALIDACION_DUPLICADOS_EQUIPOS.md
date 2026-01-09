# Validación de Duplicados para Equipos de Cómputo

## Descripción

Se ha implementado un sistema de validación en tiempo real para detectar números de serie e inventario duplicados al agregar equipos de cómputo. Cuando se detecta un duplicado, aparece una **ventana flotante (modal)** con información detallada del equipo existente.

## Características

### 1. Validación en Tiempo Real
- La validación se ejecuta automáticamente cuando el usuario sale del campo (evento `blur`)
- Se muestra un indicador de carga mientras se verifica el duplicado
- Los campos se marcan visualmente como válidos (verde) o inválidos (rojo)

### 2. Ventana Flotante (Modal)
Cuando se detecta un duplicado, aparece un modal de SweetAlert2 con:
- **Icono de advertencia** (⚠️)
- **Título**: "¡Número Duplicado!"
- **Información del campo duplicado**: Número de Serie o Número de Inventario
- **Detalles del equipo existente**: Marca, Modelo, Serie e Inventario
- **Mensaje informativo**: Solicita ingresar un número diferente

### 3. Prevención de Envío
- El formulario no se puede enviar si hay duplicados detectados
- Se muestra una alerta adicional si el usuario intenta enviar con duplicados

## Archivos Modificados

### 1. **SiteController.php**
- **Ubicación**: `frontend/controllers/SiteController.php`
- **Método**: `actionVerificarDuplicado()`
- **Función**: Endpoint AJAX que verifica duplicados en la base de datos

**Mejoras realizadas**:
```php
- Mapeo correcto de campos para modelo Equipo (NUM_SERIE, NUM_INVENTARIO)
- Retorno de información detallada del dispositivo duplicado
- Método obtenerInfoDispositivo() para formatear la información del equipo
```

### 2. **validacion-duplicados.js**
- **Ubicación**: `frontend/web/js/validacion-duplicados.js`
- **Función**: Script JavaScript para validación en tiempo real

**Características**:
```javascript
- Detecta campos con múltiples variaciones de nombres
- Validación AJAX con indicador de carga
- Modal SweetAlert2 con información detallada
- Prevención de envío del formulario
```

### 3. **agregar.php (Vista)**
- **Ubicación**: `frontend/views/site/equipo/agregar.php`
- **Función**: Formulario de agregar equipos

**Configuración**:
```php
- Carga de SweetAlert2 desde CDN
- Registro del script validacion-duplicados.js
- Inicialización: inicializarValidacionDuplicados('Equipo')
```

## Flujo de Funcionamiento

1. **Usuario completa el campo** de Número de Serie o Inventario
2. **Usuario sale del campo** (blur event)
3. **JavaScript envía petición AJAX** a `/site/verificar-duplicado`
4. **Controlador verifica** en la base de datos si existe el número
5. **Si existe duplicado**:
   - Campo se marca como inválido (rojo)
   - Se muestra modal con información del equipo existente
   - Se previene el envío del formulario
6. **Si no existe duplicado**:
   - Campo se marca como válido (verde)
   - Usuario puede continuar

## Ejemplo de Modal

Cuando se detecta un duplicado, el usuario ve:

```
┌─────────────────────────────────────────────┐
│              ⚠️ ¡Número Duplicado!          │
├─────────────────────────────────────────────┤
│                                             │
│  Número de Serie: 1210802025                │
│                                             │
│  Este número ya está registrado en:         │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ ⚠️ Dell - Imac 2017                  │   │
│  │    Serie: 1210802025                 │   │
│  │    Inventario: 1210802025            │   │
│  └─────────────────────────────────────┘   │
│                                             │
│  ℹ️ Por favor, ingresa un número            │
│     diferente para continuar.               │
│                                             │
│           [ Entendido ]                     │
└─────────────────────────────────────────────┘
```

## Validación para Otros Tipos de Equipos

El sistema está preparado para validar duplicados en:

- ✅ Equipos de Cómputo (Equipo)
- ✅ No Break / UPS (Nobreak)
- ✅ Monitores (Monitor)
- ✅ Impresoras (Impresora)
- ✅ Conectividad (Conectividad)
- ✅ Telefonía (Telefonia)
- ✅ Video Vigilancia (VideoVigilancia)
- ✅ Memoria RAM (Ram)
- ✅ Almacenamiento (Almacenamiento)
- ✅ Equipo de Sonido (Sonido)
- ✅ Adaptadores (Adaptador)
- ✅ Micrófonos (Microfono)
- ✅ Baterías (Bateria)
- ✅ Procesadores (Procesador)
- ✅ Fuentes de Poder (FuentesDePoder)

## Configuración por Modelo

Para activar la validación en otras vistas, agrega al final de la vista:

```php
<?php
// Registrar SweetAlert2
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', 
    ['position' => \yii\web\View::POS_HEAD]);

// Registrar el script de validación de duplicados
$this->registerJsFile('@web/js/validacion-duplicados.js', 
    ['depends' => [\yii\web\JqueryAsset::class]]);

// Inicializar validación (cambiar 'Equipo' por el modelo correspondiente)
$this->registerJs("inicializarValidacionDuplicados('Equipo');", 
    \yii\web\View::POS_READY);
?>
```

## Depuración

### Verificar que SweetAlert2 esté cargado
```javascript
// En la consola del navegador
console.log(typeof Swal); // Debe mostrar "function"
```

### Verificar que la validación esté inicializada
```javascript
// En la consola del navegador
console.log(modeloActual); // Debe mostrar "Equipo"
```

### Verificar peticiones AJAX
1. Abrir **DevTools** (F12)
2. Ir a la pestaña **Network**
3. Escribir un número en el campo de Serie o Inventario
4. Salir del campo (blur)
5. Debe aparecer una petición a `verificar-duplicado`

### Errores Comunes

#### 1. Modal no aparece
- **Causa**: SweetAlert2 no está cargado
- **Solución**: Verificar que el CDN esté accesible

#### 2. No se valida al salir del campo
- **Causa**: JavaScript no está inicializado
- **Solución**: Verificar que `inicializarValidacionDuplicados()` se ejecute

#### 3. Error 404 en AJAX
- **Causa**: Ruta incorrecta al endpoint
- **Solución**: Verificar la ruta en `validacion-duplicados.js`

#### 4. Validación no encuentra duplicados existentes
- **Causa**: Nombres de campos incorrectos en el controlador
- **Solución**: Verificar mapeo de campos en `actionVerificarDuplicado()`

## Pruebas

### Caso 1: Agregar equipo con número de serie único
1. Ir a "Agregar Equipo de Cómputo"
2. Llenar el formulario con un número de serie nuevo
3. Salir del campo → Campo debe marcarse en **verde**
4. Enviar formulario → Debe guardarse correctamente

### Caso 2: Agregar equipo con número de serie duplicado
1. Ir a "Agregar Equipo de Cómputo"
2. Llenar el campo "Número de Serie" con un valor existente (ej: 1210802025)
3. Salir del campo → Debe aparecer **modal de advertencia**
4. Campo debe marcarse en **rojo**
5. Intentar enviar formulario → Debe mostrar alerta de duplicados

### Caso 3: Agregar equipo con número de inventario duplicado
1. Ir a "Agregar Equipo de Cómputo"
2. Llenar el campo "Número de Inventario" con un valor existente
3. Salir del campo → Debe aparecer **modal de advertencia**
4. Campo debe marcarse en **rojo**
5. Cambiar por un valor único → Campo debe marcarse en **verde**

## Personalización

### Cambiar el diseño del modal
Editar en `validacion-duplicados.js` la función `mostrarAlertaDuplicado()`:

```javascript
Swal.fire({
    icon: 'warning',           // Cambiar icono
    title: 'Título',           // Cambiar título
    confirmButtonColor: '#d33', // Cambiar color del botón
    // ... otras opciones
});
```

### Agregar validación adicional
Editar en `validacion-duplicados.js` la función `validarDuplicado()`:

```javascript
// Agregar validación personalizada antes de enviar AJAX
if (valor.length < 5) {
    // Lógica personalizada
}
```

## Mantenimiento

- ✅ Código documentado y comentado
- ✅ Funciones reutilizables
- ✅ Compatible con múltiples modelos
- ✅ Fácil de extender
- ✅ Sin dependencias externas (excepto SweetAlert2)

---

**Fecha de implementación**: Enero 2026  
**Desarrollado para**: Sistema de Altas y Bajas de Equipos  
**Tecnologías**: PHP (Yii2), JavaScript (jQuery), SweetAlert2
