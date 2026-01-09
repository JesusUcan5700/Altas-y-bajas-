# 🔍 Guía de Depuración - Validación de Duplicados

## ⚠️ PROBLEMA DETECTADO
Los mensajes de duplicado aparecen como texto bajo los campos, pero **NO aparece la ventana flotante (modal de SweetAlert2)**.

## ✅ SOLUCIÓN IMPLEMENTADA

He actualizado el código con **logs de depuración** para identificar exactamente dónde está el problema.

## 📋 PASOS PARA DEPURAR

### 1️⃣ Abre el formulario de Agregar Equipo
- Ve a: http://localhost/altas_bajas/frontend/web/index.php?r=site/equipo-agregar
- O desde el menú: "Agregar Equipo de Cómputo"

### 2️⃣ Abre la Consola de Desarrollador
- Presiona **F12** en el navegador
- Ve a la pestaña **Console**

### 3️⃣ Revisa los mensajes iniciales
Deberías ver algo como:
```
[Equipo Agregar] Inicializando validación de duplicados
[Equipo Agregar] jQuery disponible: true
[Equipo Agregar] SweetAlert2 disponible: true
[Validación Duplicados] Inicializando para modelo: Equipo
[Validación Duplicados] Campos de serie encontrados: 1
[Validación Duplicados] Campo serie - ID: equipo-num_serie Name: Equipo[NUM_SERIE]
[Validación Duplicados] Campos de inventario encontrados: 1
[Validación Duplicados] Campo inventario - ID: equipo-num_inventario Name: Equipo[NUM_INVENTARIO]
[Validación Duplicados] Inicialización completada

=== DIAGNÓSTICO DE VALIDACIÓN DE DUPLICADOS ===
1. jQuery:
   ✓ jQuery cargado (versión: X.X.X)
2. SweetAlert2:
   ✓ SweetAlert2 cargado
   ✓ Modal de prueba mostrado
...
```

### 4️⃣ Prueba escribir un número duplicado
1. En el campo "Número de Serie" escribe: **1210802025**
2. Haz clic FUERA del campo (o presiona Tab)
3. Revisa la consola, deberías ver:
```
[Validación Duplicados] Blur en campo de serie
[Validación Duplicados] Validando serie: 1210802025
[Validación Duplicados] Enviando petición AJAX...
[Validación Duplicados] CSRF Token: OK
[Validación Duplicados] Respuesta recibida: {existe: true, mensaje: "...", dispositivo: "..."}
[Validación Duplicados] DUPLICADO ENCONTRADO!
[Validación Duplicados] Mostrando modal de duplicado
[Validación Duplicados] SweetAlert2 disponible: true
[Validación Duplicados] Modal abierto
```

### 5️⃣ Verifica errores
Si ves algún error en rojo en la consola, **copia y pégalo** para analizarlo.

## 🧪 PRUEBA MANUAL DESDE LA CONSOLA

Si el modal NO aparece automáticamente, prueba esto en la consola:

### A) Verificar que todo esté cargado
```javascript
diagnosticoValidacion()
```

### B) Probar validación manual
```javascript
probarValidacionManual("serie", "1210802025")
```

### C) Probar SweetAlert2 directamente
```javascript
Swal.fire({
    icon: 'warning',
    title: '¡Prueba!',
    text: 'Si ves esto, SweetAlert2 funciona'
})
```

## 🐛 PROBLEMAS COMUNES Y SOLUCIONES

### ❌ Error: "SweetAlert2 no está cargado"
**Solución**: 
- Verifica tu conexión a Internet (usa CDN)
- O descarga SweetAlert2 localmente

### ❌ Error: "Función inicializarValidacionDuplicados NO encontrada"
**Solución**: 
- El archivo `validacion-duplicados.js` no se cargó
- Verifica la ruta: `frontend/web/js/validacion-duplicados.js`

### ❌ Error 404 en AJAX
**Solución**: 
- Verifica que la URL sea correcta: `/altas_bajas/frontend/web/index.php?r=site/verificar-duplicado`
- Ajusta según tu configuración

### ❌ Error CSRF
**Solución**: 
- El token CSRF no se está enviando correctamente
- Verifica que Yii2 esté configurado para generar el token

### ❌ Los campos no se encuentran
**Solución**: 
- Los selectores jQuery no están encontrando los campos
- Verifica en la consola cuántos campos se encontraron

## 📸 CAPTURAS ESPERADAS

### Consola al cargar la página:
```
[Equipo Agregar] Inicializando validación de duplicados
[Validación Duplicados] Campos de serie encontrados: 1
[Validación Duplicados] Campos de inventario encontrados: 1
✓ jQuery cargado
✓ SweetAlert2 cargado
```

### Consola al validar duplicado:
```
[Validación Duplicados] Blur en campo de serie
[Validación Duplicados] Validando serie: 1210802025
[Validación Duplicados] DUPLICADO ENCONTRADO!
[Validación Duplicados] Mostrando modal de duplicado
```

### Modal que debe aparecer:
```
┌─────────────────────────────────────────────┐
│          ⚠️ ¡Número Duplicado!              │
├─────────────────────────────────────────────┤
│                                             │
│  Número de Serie: 1210802025                │
│                                             │
│  Este número ya está registrado en:         │
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │ ⚠️ Dell - Imac 2017                  │   │
│  │    (Serie: 1210802025,               │   │
│  │     Inventario: 1210802025)          │   │
│  └─────────────────────────────────────┘   │
│                                             │
│           [ Entendido ]                     │
└─────────────────────────────────────────────┘
```

## 📞 QUÉ REPORTAR SI NO FUNCIONA

Envía esta información:

1. **Mensajes de la consola** al cargar la página
2. **Mensajes de la consola** al validar un número
3. **Errores en rojo** (si los hay)
4. **Resultado de** `diagnosticoValidacion()`
5. **Captura de pantalla** del formulario

## ✨ ARCHIVOS ACTUALIZADOS

- ✅ `frontend/web/js/validacion-duplicados.js` - Logs de depuración añadidos
- ✅ `frontend/views/site/equipo/agregar.php` - Script de diagnóstico incluido
- ✅ `frontend/web/js/diagnostico-validacion.js` - Script de diagnóstico creado
- ✅ `frontend/controllers/SiteController.php` - Endpoint verificado

## 🎯 PRÓXIMOS PASOS

1. **Recarga** la página del formulario (Ctrl + F5)
2. **Abre** la consola (F12)
3. **Escribe** un número duplicado
4. **Sal** del campo (Tab o clic fuera)
5. **Copia** todos los mensajes de la consola
6. **Comparte** los mensajes para identificar el problema exacto

---

**Última actualización**: Enero 2, 2026  
**Modo**: Depuración activa 🔍
