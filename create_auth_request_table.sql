-- Script SQL para crear la tabla auth_request manualmente
-- Si no quieres usar migraciones de Yii2
-- Ejecuta esto directamente en tu base de datos MySQL

CREATE TABLE IF NOT EXISTS `auth_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_completo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=Pendiente, 1=Aprobado, 2=Rechazado',
  `approval_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `magic_link_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expiry` int(11) DEFAULT NULL,
  `approved_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email del aprobador',
  `approved_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `last_login` int(11) DEFAULT NULL,
  `login_count` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-auth_request-approval_token` (`approval_token`),
  UNIQUE KEY `idx-auth_request-magic_link_token` (`magic_link_token`),
  KEY `idx-auth_request-email` (`email`),
  KEY `idx-auth_request-status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar que la tabla se creó correctamente
SELECT 'Tabla auth_request creada exitosamente' as resultado;

-- Mostrar estructura de la tabla
DESCRIBE auth_request;
