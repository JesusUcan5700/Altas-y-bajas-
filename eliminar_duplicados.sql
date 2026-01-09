-- Script para eliminar registros duplicados en las tablas de equipos
-- Este script mantiene el registro con el ID más bajo y elimina los duplicados

-- IMPORTANTE: Haz un backup de la base de datos antes de ejecutar este script

-- 1. Eliminar duplicados en la tabla nobreak
DELETE n1 FROM nobreak n1
INNER JOIN nobreak n2 
WHERE n1.idNOBREAK > n2.idNOBREAK
  AND n1.MARCA = n2.MARCA
  AND n1.MODELO = n2.MODELO
  AND n1.NUMERO_SERIE = n2.NUMERO_SERIE
  AND n1.NUMERO_INVENTARIO = n2.NUMERO_INVENTARIO
  AND n1.Estado = n2.Estado;

-- 2. Eliminar duplicados en la tabla fuentesdepoder
DELETE f1 FROM fuentesdepoder f1
INNER JOIN fuentesdepoder f2 
WHERE f1.idFuentePoder > f2.idFuentePoder
  AND f1.MARCA = f2.MARCA
  AND f1.MODELO = f2.MODELO
  AND f1.NUMERO_SERIE = f2.NUMERO_SERIE
  AND f1.NUMERO_INVENTARIO = f2.NUMERO_INVENTARIO
  AND f1.ESTADO = f2.ESTADO;

-- 3. Eliminar duplicados en la tabla equipo
DELETE e1 FROM equipo e1
INNER JOIN equipo e2 
WHERE e1.idEquipo > e2.idEquipo
  AND e1.MARCA = e2.MARCA
  AND e1.MODELO = e2.MODELO
  AND e1.NUM_SERIE = e2.NUM_SERIE
  AND e1.NUM_INVENTARIO = e2.NUM_INVENTARIO
  AND e1.Estado = e2.Estado;

-- 4. Eliminar duplicados en la tabla impresora
DELETE i1 FROM impresora i1
INNER JOIN impresora i2 
WHERE i1.idImpresora > i2.idImpresora
  AND i1.MARCA = i2.MARCA
  AND i1.MODELO = i2.MODELO
  AND i1.NUMERO_SERIE = i2.NUMERO_SERIE
  AND i1.NUMERO_INVENTARIO = i2.NUMERO_INVENTARIO
  AND i1.Estado = i2.Estado;

-- 5. Eliminar duplicados en la tabla monitor
DELETE m1 FROM monitor m1
INNER JOIN monitor m2 
WHERE m1.idMonitor > m2.idMonitor
  AND m1.MARCA = m2.MARCA
  AND m1.MODELO = m2.MODELO
  AND m1.NUMERO_SERIE = m2.NUMERO_SERIE
  AND m1.NUMERO_INVENTARIO = m2.NUMERO_INVENTARIO
  AND m1.Estado = m2.Estado;

-- 6. Eliminar duplicados en la tabla adaptador
DELETE a1 FROM adaptador a1
INNER JOIN adaptador a2 
WHERE a1.idAdaptador > a2.idAdaptador
  AND a1.MARCA = a2.MARCA
  AND a1.MODELO = a2.MODELO
  AND a1.NUMERO_SERIE = a2.NUMERO_SERIE
  AND a1.NUMERO_INVENTARIO = a2.NUMERO_INVENTARIO
  AND a1.ESTADO = a2.ESTADO;

-- 7. Eliminar duplicados en la tabla bateria
DELETE b1 FROM bateria b1
INNER JOIN bateria b2 
WHERE b1.idBateria > b2.idBateria
  AND b1.MARCA = b2.MARCA
  AND b1.MODELO = b2.MODELO
  AND b1.NUMERO_SERIE = b2.NUMERO_SERIE
  AND b1.NUMERO_INVENTARIO = b2.NUMERO_INVENTARIO
  AND b1.ESTADO = b2.ESTADO;

-- 8. Eliminar duplicados en la tabla almacenamiento
DELETE a1 FROM almacenamiento a1
INNER JOIN almacenamiento a2 
WHERE a1.idALMACENAMIENTO > a2.idALMACENAMIENTO
  AND a1.MARCA = a2.MARCA
  AND a1.MODELO = a2.MODELO
  AND a1.NUMERO_SERIE = a2.NUMERO_SERIE
  AND a1.NUMERO_INVENTARIO = a2.NUMERO_INVENTARIO
  AND a1.ESTADO = a2.ESTADO;

-- 9. Eliminar duplicados en la tabla ram
DELETE r1 FROM ram r1
INNER JOIN ram r2 
WHERE r1.idRAM > r2.idRAM
  AND r1.MARCA = r2.MARCA
  AND r1.MODELO = r2.MODELO
  AND r1.NUMERO_SERIE = r2.NUMERO_SERIE
  AND r1.NUMERO_INVENTARIO = r2.NUMERO_INVENTARIO
  AND r1.ESTADO = r2.ESTADO;

-- 10. Eliminar duplicados en la tabla sonido
DELETE s1 FROM sonido s1
INNER JOIN sonido s2 
WHERE s1.idSonido > s2.idSonido
  AND s1.MARCA = s2.MARCA
  AND s1.MODELO = s2.MODELO
  AND s1.NUMERO_SERIE = s2.NUMERO_SERIE
  AND s1.NUMERO_INVENTARIO = s2.NUMERO_INVENTARIO
  AND s1.ESTADO = s2.ESTADO;

-- 11. Eliminar duplicados en la tabla procesador
DELETE p1 FROM procesador p1
INNER JOIN procesador p2 
WHERE p1.idProcesador > p2.idProcesador
  AND p1.MARCA = p2.MARCA
  AND p1.MODELO = p2.MODELO
  AND p1.NUMERO_SERIE = p2.NUMERO_SERIE
  AND p1.NUMERO_INVENTARIO = p2.NUMERO_INVENTARIO
  AND p1.Estado = p2.Estado;

-- 12. Eliminar duplicados en la tabla conectividad
DELETE c1 FROM conectividad c1
INNER JOIN conectividad c2 
WHERE c1.idConectividad > c2.idConectividad
  AND c1.MARCA = c2.MARCA
  AND c1.MODELO = c2.MODELO
  AND c1.NUMERO_SERIE = c2.NUMERO_SERIE
  AND c1.NUMERO_INVENTARIO = c2.NUMERO_INVENTARIO
  AND c1.ESTADO = c2.ESTADO;

-- 13. Eliminar duplicados en la tabla telefonia
DELETE t1 FROM telefonia t1
INNER JOIN telefonia t2 
WHERE t1.idTelefonia > t2.idTelefonia
  AND t1.MARCA = t2.MARCA
  AND t1.MODELO = t2.MODELO
  AND t1.NUMERO_SERIE = t2.NUMERO_SERIE
  AND t1.NUMERO_INVENTARIO = t2.NUMERO_INVENTARIO
  AND t1.ESTADO = t2.ESTADO;

-- 14. Eliminar duplicados en la tabla videovigilancia
DELETE v1 FROM videovigilancia v1
INNER JOIN videovigilancia v2 
WHERE v1.idVideoVigilancia > v2.idVideoVigilancia
  AND v1.MARCA = v2.MARCA
  AND v1.MODELO = v2.MODELO
  AND v1.NUMERO_SERIE = v2.NUMERO_SERIE
  AND v1.NUMERO_INVENTARIO = v2.NUMERO_INVENTARIO
  AND v1.ESTADO = v2.ESTADO;

-- Mostrar resumen de registros después de la limpieza
SELECT 'nobreak' as tabla, COUNT(*) as total FROM nobreak WHERE Estado = 'BAJA'
UNION ALL
SELECT 'fuentesdepoder', COUNT(*) FROM fuentesdepoder WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'equipo', COUNT(*) FROM equipo WHERE Estado = 'BAJA'
UNION ALL
SELECT 'impresora', COUNT(*) FROM impresora WHERE Estado = 'BAJA'
UNION ALL
SELECT 'monitor', COUNT(*) FROM monitor WHERE Estado = 'BAJA'
UNION ALL
SELECT 'adaptador', COUNT(*) FROM adaptador WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'bateria', COUNT(*) FROM bateria WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'almacenamiento', COUNT(*) FROM almacenamiento WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'ram', COUNT(*) FROM ram WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'sonido', COUNT(*) FROM sonido WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'procesador', COUNT(*) FROM procesador WHERE Estado = 'BAJA'
UNION ALL
SELECT 'conectividad', COUNT(*) FROM conectividad WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'telefonia', COUNT(*) FROM telefonia WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'videovigilancia', COUNT(*) FROM videovigilancia WHERE ESTADO = 'BAJA';
