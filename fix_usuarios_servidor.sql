-- ============================================================
-- EJECUTAR EN EL SERVIDOR DESPUÉS DE HACER git pull
-- Esto desactiva TODOS los usuarios excepto el admin
-- para que necesiten re-aprobación
-- ============================================================

-- 1. Ver usuarios actuales antes de modificar
SELECT id, username, email, status, 
       CASE status 
           WHEN 0 THEN 'ELIMINADO' 
           WHEN 9 THEN 'INACTIVO (pendiente)' 
           WHEN 10 THEN 'ACTIVO' 
       END as estado_texto
FROM user ORDER BY id;

-- 2. Desactivar TODOS los usuarios que no sean admin
-- Cambia 'admin' por el username real del administrador
UPDATE user SET status = 9 
WHERE username != 'admin' 
AND status = 10;

-- 3. Verificar el cambio
SELECT id, username, email, status,
       CASE status 
           WHEN 0 THEN 'ELIMINADO' 
           WHEN 9 THEN 'INACTIVO (pendiente)' 
           WHEN 10 THEN 'ACTIVO' 
       END as estado_texto
FROM user ORDER BY id;

-- 4. También limpiar auth_requests existentes que estén aprobados
-- para forzar re-aprobación
UPDATE auth_request SET status = 0 
WHERE status = 1;

-- 5. Verificar auth_requests
SELECT id, email, nombre_completo, status,
       CASE status 
           WHEN 0 THEN 'PENDIENTE' 
           WHEN 1 THEN 'APROBADO' 
           WHEN 2 THEN 'RECHAZADO' 
       END as estado_texto
FROM auth_request ORDER BY id;
