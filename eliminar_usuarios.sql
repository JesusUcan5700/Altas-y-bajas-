-- ============================================================
-- ADMINISTRACIÓN DE USUARIOS
-- Comandos SQL para eliminar usuarios y liberar correos
-- ============================================================

-- ============================
-- 1. VER TODOS LOS USUARIOS
-- ============================
SELECT u.id, u.username, u.email, u.status,
    CASE u.status 
        WHEN 0 THEN 'ELIMINADO' 
        WHEN 9 THEN 'INACTIVO (pendiente)' 
        WHEN 10 THEN 'ACTIVO' 
    END as estado
FROM user u ORDER BY u.id;

-- ============================
-- 2. VER SOLICITUDES DE AUTH
-- ============================
SELECT id, email, nombre_completo, status,
    CASE status 
        WHEN 0 THEN 'PENDIENTE' 
        WHEN 1 THEN 'APROBADO' 
        WHEN 2 THEN 'RECHAZADO' 
    END as estado
FROM auth_request ORDER BY id;

-- ============================================================
-- 3. ELIMINAR UN USUARIO Y LIBERAR SU CORREO
--    Cambia 'correo@ejemplo.com' por el email real
-- ============================================================
DELETE FROM auth_request WHERE email = 'correo@ejemplo.com';
DELETE FROM user WHERE email = 'correo@ejemplo.com';

-- ============================================================
-- 4. ELIMINAR VARIOS USUARIOS A LA VEZ
--    Agrega los correos que necesites
-- ============================================================
-- DELETE FROM auth_request WHERE email IN ('correo1@ejemplo.com', 'correo2@ejemplo.com');
-- DELETE FROM user WHERE email IN ('correo1@ejemplo.com', 'correo2@ejemplo.com');

-- ============================================================
-- 5. ELIMINAR TODOS LOS USUARIOS EXCEPTO ADMIN
--    ⚠️ CUIDADO: Esto borra TODOS los usuarios no-admin
-- ============================================================
-- DELETE FROM auth_request;
-- DELETE FROM user WHERE username != 'admin';
