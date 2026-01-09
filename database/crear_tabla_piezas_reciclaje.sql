-- =====================================================
-- Script para crear la tabla de piezas de reciclaje
-- Sistema de Gestión de Altas y Bajas
-- Fecha: 2025-12-04
-- =====================================================

-- Tabla principal para almacenar piezas recuperadas para reciclaje
DROP TABLE IF EXISTS `piezas_reciclaje`;
CREATE TABLE IF NOT EXISTS `piezas_reciclaje` (
    `id` int NOT NULL AUTO_INCREMENT,
    `tipo_pieza` varchar(50) NOT NULL COMMENT 'Tipo de pieza: RAM, Procesador, Almacenamiento, Monitor, Fuente de Poder, etc.',
    `marca` varchar(100) NOT NULL COMMENT 'Marca del componente',
    `modelo` varchar(100) DEFAULT NULL COMMENT 'Modelo del componente',
    `especificaciones` varchar(255) DEFAULT NULL COMMENT 'Especificaciones técnicas (capacidad, frecuencia, etc.)',
    `numero_serie` varchar(100) DEFAULT NULL COMMENT 'Número de serie del componente',
    `numero_inventario` varchar(100) DEFAULT NULL COMMENT 'Número de inventario asignado',
    `estado_pieza` varchar(50) NOT NULL DEFAULT 'Disponible' COMMENT 'Estado: Disponible, En Uso, Reservado, Dañado',
    `condicion` varchar(100) NOT NULL DEFAULT 'Bueno' COMMENT 'Condición física: Excelente, Bueno, Regular, Malo',
    `equipo_origen` varchar(100) DEFAULT NULL COMMENT 'ID o referencia del equipo de donde se extrajo',
    `equipo_origen_descripcion` varchar(255) DEFAULT NULL COMMENT 'Descripción del equipo de origen',
    `componente_defectuoso` varchar(255) DEFAULT NULL COMMENT 'Qué componente del equipo original estaba defectuoso',
    `motivo_recuperacion` text COMMENT 'Motivo por el cual se recuperó la pieza',
    `ubicacion_almacen` varchar(100) DEFAULT NULL COMMENT 'Ubicación física donde se almacena la pieza',
    `observaciones` text COMMENT 'Observaciones adicionales sobre la pieza',
    `asignado_a` varchar(255) DEFAULT NULL COMMENT 'Equipo o reparación donde está asignada la pieza',
    `fecha_recuperacion` date NOT NULL COMMENT 'Fecha en que se recuperó la pieza',
    `fecha_asignacion` date DEFAULT NULL COMMENT 'Fecha en que se asignó a una reparación',
    `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    `fecha_ultima_edicion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Última modificación',
    `usuario_registro` varchar(100) DEFAULT NULL COMMENT 'Usuario que registró la pieza',
    `ultimo_editor` varchar(100) DEFAULT NULL COMMENT 'Último usuario que editó el registro',
    PRIMARY KEY (`id`),
    KEY `idx_tipo_pieza` (`tipo_pieza`),
    KEY `idx_estado_pieza` (`estado_pieza`),
    KEY `idx_condicion` (`condicion`),
    KEY `idx_fecha_recuperacion` (`fecha_recuperacion`),
    KEY `idx_numero_serie` (`numero_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Piezas recuperadas de equipos dados de baja para reciclaje';

-- =====================================================
-- Tabla para historial de movimientos de piezas
-- =====================================================
DROP TABLE IF EXISTS `historial_piezas_reciclaje`;
CREATE TABLE IF NOT EXISTS `historial_piezas_reciclaje` (
    `id` int NOT NULL AUTO_INCREMENT,
    `pieza_id` int NOT NULL COMMENT 'ID de la pieza en piezas_reciclaje',
    `accion` varchar(50) NOT NULL COMMENT 'Tipo de acción: Registro, Asignación, Devolución, Baja, Edición',
    `estado_anterior` varchar(50) DEFAULT NULL COMMENT 'Estado antes del cambio',
    `estado_nuevo` varchar(50) DEFAULT NULL COMMENT 'Estado después del cambio',
    `equipo_destino` varchar(100) DEFAULT NULL COMMENT 'Equipo al que se asignó (si aplica)',
    `observaciones` text COMMENT 'Observaciones del movimiento',
    `usuario` varchar(100) DEFAULT NULL COMMENT 'Usuario que realizó la acción',
    `fecha_movimiento` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del movimiento',
    PRIMARY KEY (`id`),
    KEY `idx_pieza_id` (`pieza_id`),
    KEY `idx_fecha_movimiento` (`fecha_movimiento`),
    KEY `idx_accion` (`accion`),
    CONSTRAINT `fk_historial_pieza` FOREIGN KEY (`pieza_id`) REFERENCES `piezas_reciclaje` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Historial de movimientos de piezas de reciclaje';

-- =====================================================
-- Insertar algunos datos de ejemplo
-- =====================================================
INSERT INTO `piezas_reciclaje` (`tipo_pieza`, `marca`, `modelo`, `especificaciones`, `numero_serie`, `estado_pieza`, `condicion`, `equipo_origen`, `equipo_origen_descripcion`, `componente_defectuoso`, `motivo_recuperacion`, `ubicacion_almacen`, `fecha_recuperacion`, `usuario_registro`) VALUES
('Memoria RAM', 'Kingston', 'ValueRAM', '8GB DDR4 2666MHz', 'KVR26N19S8-001', 'Disponible', 'Excelente', 'E001', 'Computadora Dell OptiPlex 3080', 'Disco Duro defectuoso', 'Equipo dado de baja por falla de almacenamiento', 'Estante A-1', CURDATE(), 'Admin'),
('Disco Duro', 'Seagate', 'Barracuda', '500GB SATA 7200RPM', 'ST500DM002-123', 'Disponible', 'Bueno', 'E005', 'Computadora HP ProDesk 400', 'Tarjeta madre dañada', 'Recuperado de equipo con falla de motherboard', 'Estante A-2', CURDATE(), 'Admin'),
('Fuente de Poder', 'Corsair', 'VS650', '650W 80+ Bronze', 'VS650-ABC123', 'Disponible', 'Bueno', 'E012', 'Computadora personalizada', 'Procesador quemado', 'Fuente funcional de equipo con CPU dañado', 'Estante B-1', CURDATE(), 'Admin'),
('Procesador', 'Intel', 'Core i5-10400', '2.9GHz 6 Núcleos', 'INTEL-I5-XYZ', 'Reservado', 'Excelente', 'E008', 'Workstation Dell Precision', 'Monitor sin imagen', 'CPU recuperado para futuras reparaciones', 'Estante C-1', CURDATE(), 'Admin'),
('Monitor', 'Samsung', 'S24F350', '24" Full HD', 'SAM-24F-789', 'En Uso', 'Bueno', 'E015', 'Estación de trabajo', 'CPU sin arranque', 'Monitor en buenas condiciones', 'Asignado', CURDATE(), 'Admin');

-- Insertar historial para las piezas de ejemplo
INSERT INTO `historial_piezas_reciclaje` (`pieza_id`, `accion`, `estado_anterior`, `estado_nuevo`, `observaciones`, `usuario`) VALUES
(1, 'Registro', NULL, 'Disponible', 'Pieza registrada en inventario de reciclaje', 'Admin'),
(2, 'Registro', NULL, 'Disponible', 'Pieza registrada en inventario de reciclaje', 'Admin'),
(3, 'Registro', NULL, 'Disponible', 'Pieza registrada en inventario de reciclaje', 'Admin'),
(4, 'Registro', NULL, 'Disponible', 'Pieza registrada en inventario de reciclaje', 'Admin'),
(4, 'Cambio Estado', 'Disponible', 'Reservado', 'Reservada para reparación pendiente', 'Admin'),
(5, 'Registro', NULL, 'Disponible', 'Pieza registrada en inventario de reciclaje', 'Admin'),
(5, 'Asignación', 'Disponible', 'En Uso', 'Asignado a reparación de equipo E020', 'Admin');

SELECT 'Tablas creadas correctamente' AS mensaje;
