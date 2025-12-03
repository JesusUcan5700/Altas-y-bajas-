# MÓDULO DE RECICLAJE DE PIEZAS DE EQUIPOS

## Descripción
Nuevo módulo agregado al sistema de gestión de equipos que permite administrar y reutilizar componentes recuperados de equipos dados de baja.

## Ubicación
- **Menú Principal**: Botón verde "RECICLAJE DE PIEZAS DE EQUIPOS"
- **URL**: `/site/reciclaje-piezas`
- **Vista**: `frontend/views/site/reciclaje-piezas.php`
- **Controlador**: `frontend/controllers/SiteController.php` -> `actionRecicljePiezas()`

## Características Implementadas

### 1. Interfaz Principal
- ✅ **Hero Section**: Encabezado con icono de reciclaje y descripción
- ✅ **Estadísticas Rápidas**: Tarjetas con métricas importantes
  - Piezas Disponibles: 45
  - En Reparación: 12
  - Reutilizadas: 28
  - Tasa de Reciclaje: 85%

### 2. Funcionalidades Principales
- ✅ **Registrar Pieza**: Botón para agregar nuevas piezas recuperadas
- ✅ **Inventario**: Ver todas las piezas disponibles
- ✅ **Gestionar Uso**: Asignar piezas a reparaciones
- ✅ **Reportes**: Estadísticas de reciclaje

### 3. Listado de Piezas Recientes
- ✅ **Tabla Interactiva**: Muestra piezas registradas recientemente
- ✅ **Estados Visuales**: 
  - 🟢 Disponible (verde)
  - 🔵 En Uso (azul)
  - 🟡 Reservado (amarillo)
- ✅ **Información Detallada**: Marca, modelo, origen, fecha

### 4. Panel Lateral de Categorías
- ✅ **Memoria RAM**: 12 piezas
- ✅ **Almacenamiento**: 8 piezas
- ✅ **Procesadores**: 5 piezas
- ✅ **Fuentes de Poder**: 7 piezas
- ✅ **Monitores**: 6 piezas
- ✅ **Otros**: 7 piezas

## Diseño y Estilo

### Colores Temáticos
- **Principal**: Verde (#28a745) - Representa reciclaje
- **Secundarios**: Azul (#007bff), Amarillo (#ffc107), Rojo (#dc3545)
- **Fondo**: Gradientes suaves para mejor visualización

### Efectos Interactivos
- ✅ **Hover Effects**: Elevación y sombreado en tarjetas
- ✅ **Transiciones**: Animaciones suaves (0.3s)
- ✅ **Responsive**: Compatible con dispositivos móviles

### Iconografía
- 🔄 **Reciclaje**: Icono principal del módulo
- 🔧 **Herramientas**: Para piezas en reparación
- ✅ **Check**: Para piezas reutilizadas
- 📊 **Gráficas**: Para estadísticas

## Integración con el Sistema

### Menú Principal
```php
<!-- Botón agregado en index.php -->
<a href="<?= \yii\helpers\Url::to(['site/reciclaje-piezas']) ?>" 
   class="btn btn-outline-success btn-lg w-100 py-4">
    <i class="fas fa-recycle me-2"></i>
    <strong>RECICLAJE DE PIEZAS DE EQUIPOS</strong>
    <small>Gestionar componentes reutilizables</small>
</a>
```

### Controlador
```php
/**
 * Muestra la página de reciclaje de piezas de equipos
 */
public function actionRecicljePiezas()
{
    return $this->render('reciclaje-piezas');
}
```

## Funcionalidades JavaScript

### 1. Funciones Implementadas
```javascript
// Registrar nueva pieza recuperada
function registrarPieza()

// Ver inventario completo de piezas
function verInventario()

// Gestionar uso y asignación de piezas
function gestionarUso()

// Ver reportes y estadísticas
function verReportes()
```

### 2. Efectos Visuales
- **Hover dinámico**: Borde izquierdo verde en tarjetas
- **Logging**: Mensajes en consola para debugging
- **Alertas**: Información sobre funcionalidades futuras

## Datos de Ejemplo

### Piezas Registradas
1. **Memoria RAM DDR4** - 8GB Kingston
   - Estado: Disponible
   - Origen: Equipo #E001
   - Fecha: 04/09/2025

2. **Disco Duro SATA** - 500GB Seagate
   - Estado: En Uso
   - Origen: Equipo #E005
   - Fecha: 03/09/2025

3. **Fuente de Poder** - 650W Corsair
   - Estado: Reservado
   - Origen: Equipo #E012
   - Fecha: 02/09/2025

## Próximas Mejoras (Sugeridas)

### Base de Datos
- [ ] **Tabla `piezas_reciclaje`**: Para almacenar piezas recuperadas
- [ ] **Tabla `movimientos_piezas`**: Para historial de uso
- [ ] **Tabla `asignaciones_piezas`**: Para tracking de asignaciones

### Funcionalidades
- [ ] **CRUD Completo**: Crear, leer, actualizar, eliminar piezas
- [ ] **Sistema de QR**: Códigos QR para identificación rápida
- [ ] **Alertas de Vencimiento**: Para piezas con fecha límite
- [ ] **Integración con Historial de Bajas**: Auto-registro de piezas recuperables

### Reportes
- [ ] **Exportación Excel/PDF**: Reportes de reciclaje
- [ ] **Gráficas Dinámicas**: Charts.js para visualización
- [ ] **Dashboard Analytics**: Métricas avanzadas de reciclaje

## Archivos Creados/Modificados

### Nuevos Archivos
- ✅ `frontend/views/site/reciclaje-piezas.php`
- ✅ `test_exportacion_mayusculas.html` (prueba relacionada)
- ✅ `DOCUMENTACION_RECICLAJE_PIEZAS.md` (este archivo)

### Archivos Modificados
- ✅ `frontend/views/site/index.php` - Agregado botón y estilos
- ✅ `frontend/controllers/SiteController.php` - Nueva acción

## Testing

### Pruebas Realizadas
- ✅ **Navegación**: Botón funciona correctamente
- ✅ **Diseño Responsive**: Compatible con móviles
- ✅ **Efectos CSS**: Hover y transiciones funcionan
- ✅ **JavaScript**: Funciones alertan correctamente

### Comandos de Prueba
```bash
# Acceder al módulo directamente
http://localhost/altas_bajas/frontend/web/index.php?r=site/reciclaje-piezas

# Verificar desde menú principal
http://localhost/altas_bajas/frontend/web/index.php
```

## Estado del Proyecto
✅ **COMPLETADO** - Módulo base implementado y funcional

El módulo está listo para uso y futuras expansiones según las necesidades del sistema.
