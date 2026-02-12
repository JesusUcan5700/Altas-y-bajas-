-- Consultas útiles para administrar el sistema de autenticación por email

-- ============================================
-- CONSULTAS DE INFORMACIÓN
-- ============================================

-- Ver todas las solicitudes pendientes
SELECT 
    id,
    email,
    nombre_completo,
    departamento,
    FROM_UNIXTIME(created_at) as fecha_solicitud
FROM auth_request 
WHERE status = 0 
ORDER BY created_at DESC;

-- Ver usuarios aprobados
SELECT 
    id,
    email,
    nombre_completo,
    departamento,
    login_count as accesos,
    FROM_UNIXTIME(last_login) as ultimo_acceso,
    FROM_UNIXTIME(approved_at) as fecha_aprobacion
FROM auth_request 
WHERE status = 1 
ORDER BY last_login DESC;

-- Ver usuarios rechazados
SELECT 
    id,
    email,
    nombre_completo,
    FROM_UNIXTIME(approved_at) as fecha_rechazo
FROM auth_request 
WHERE status = 2 
ORDER BY approved_at DESC;

-- Estadísticas generales
SELECT 
    COUNT(*) as total_solicitudes,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as rechazadas,
    SUM(CASE WHEN status = 1 AND last_login IS NOT NULL THEN 1 ELSE 0 END) as usuarios_activos
FROM auth_request;

-- Top 10 usuarios más activos
SELECT 
    email,
    nombre_completo,
    login_count,
    FROM_UNIXTIME(last_login) as ultimo_acceso
FROM auth_request 
WHERE status = 1 
ORDER BY login_count DESC 
LIMIT 10;

-- Usuarios que nunca han ingresado (aprobados pero sin login)
SELECT 
    email,
    nombre_completo,
    FROM_UNIXTIME(approved_at) as fecha_aprobacion,
    DATEDIFF(NOW(), FROM_UNIXTIME(approved_at)) as dias_desde_aprobacion
FROM auth_request 
WHERE status = 1 
  AND last_login IS NULL
ORDER BY approved_at ASC;

-- Usuarios inactivos (más de 30 días sin acceder)
SELECT 
    email,
    nombre_completo,
    FROM_UNIXTIME(last_login) as ultimo_acceso,
    DATEDIFF(NOW(), FROM_UNIXTIME(last_login)) as dias_inactivo
FROM auth_request 
WHERE status = 1 
  AND last_login IS NOT NULL
  AND last_login < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY))
ORDER BY last_login ASC;

-- Ver tokens mágicos activos (no expirados)
SELECT 
    email,
    nombre_completo,
    FROM_UNIXTIME(token_expiry) as expira_en
FROM auth_request 
WHERE magic_link_token IS NOT NULL 
  AND token_expiry >= UNIX_TIMESTAMP()
ORDER BY token_expiry ASC;

-- ============================================
-- OPERACIONES DE MANTENIMIENTO
-- ============================================

-- Limpiar tokens expirados (ejecutar periódicamente)
UPDATE auth_request 
SET magic_link_token = NULL, 
    token_expiry = NULL 
WHERE token_expiry < UNIX_TIMESTAMP();

-- Aprobar manualmente una solicitud (usar el ID de la solicitud)
-- REEMPLAZA <ID> con el ID real y <EMAIL_ADMIN> con el email del admin
/*
UPDATE auth_request 
SET status = 1,
    approved_by = 'inventarioapoyoinformatico@valladolid.tecnm.mx',
    approved_at = UNIX_TIMESTAMP()
WHERE id = <ID>;
*/

-- Rechazar manualmente una solicitud
/*
UPDATE auth_request 
SET status = 2,
    approved_by = 'inventarioapoyoinformatico@valladolid.tecnm.mx',
    approved_at = UNIX_TIMESTAMP()
WHERE id = <ID>;
*/

-- Revocar acceso de un usuario (cambiar a rechazado)
/*
UPDATE auth_request 
SET status = 2,
    magic_link_token = NULL,
    token_expiry = NULL
WHERE email = 'usuario@example.com';
*/

-- Resetear contador de accesos de un usuario
/*
UPDATE auth_request 
SET login_count = 0
WHERE email = 'usuario@example.com';
*/

-- ============================================
-- REPORTES Y ANÁLISIS
-- ============================================

-- Solicitudes por mes (últimos 6 meses)
SELECT 
    DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m') as mes,
    COUNT(*) as total_solicitudes,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as aprobadas,
    SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as rechazadas
FROM auth_request 
WHERE created_at >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 6 MONTH))
GROUP BY DATE_FORMAT(FROM_UNIXTIME(created_at), '%Y-%m')
ORDER BY mes DESC;

-- Accesos por día (última semana)
SELECT 
    DATE(FROM_UNIXTIME(last_login)) as fecha,
    COUNT(DISTINCT id) as usuarios_unicos
FROM auth_request 
WHERE last_login >= UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY))
GROUP BY DATE(FROM_UNIXTIME(last_login))
ORDER BY fecha DESC;

-- Distribución por departamento
SELECT 
    COALESCE(departamento, 'Sin especificar') as departamento,
    COUNT(*) as total,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as aprobados,
    AVG(login_count) as promedio_accesos
FROM auth_request 
GROUP BY departamento
ORDER BY total DESC;

-- Tiempo promedio de aprobación
SELECT 
    AVG(approved_at - created_at) / 3600 as horas_promedio_aprobacion,
    MIN(approved_at - created_at) / 3600 as tiempo_minimo_horas,
    MAX(approved_at - created_at) / 3600 as tiempo_maximo_horas
FROM auth_request 
WHERE status = 1;

-- ============================================
-- LIMPIEZA Y MANTENIMIENTO
-- ============================================

-- Eliminar solicitudes rechazadas antiguas (más de 90 días)
/*
DELETE FROM auth_request 
WHERE status = 2 
  AND approved_at < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 90 DAY));
*/

-- Eliminar solicitudes pendientes muy antiguas (más de 30 días sin respuesta)
/*
DELETE FROM auth_request 
WHERE status = 0 
  AND created_at < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 30 DAY));
*/

-- ============================================
-- EXPORTACIÓN DE DATOS
-- ============================================

-- Lista completa de usuarios aprobados para exportar
SELECT 
    email as 'Correo Electrónico',
    nombre_completo as 'Nombre Completo',
    departamento as 'Departamento',
    login_count as 'Total Accesos',
    FROM_UNIXTIME(approved_at) as 'Fecha Aprobación',
    FROM_UNIXTIME(last_login) as 'Último Acceso'
FROM auth_request 
WHERE status = 1 
ORDER BY nombre_completo;

-- Log de auditoría completo
SELECT 
    id,
    email,
    nombre_completo,
    CASE status
        WHEN 0 THEN 'Pendiente'
        WHEN 1 THEN 'Aprobado'
        WHEN 2 THEN 'Rechazado'
    END as estado,
    FROM_UNIXTIME(created_at) as 'Fecha Solicitud',
    FROM_UNIXTIME(approved_at) as 'Fecha Aprobación/Rechazo',
    approved_by as 'Aprobado Por',
    login_count as 'Total Accesos',
    FROM_UNIXTIME(last_login) as 'Último Acceso'
FROM auth_request 
ORDER BY created_at DESC;
