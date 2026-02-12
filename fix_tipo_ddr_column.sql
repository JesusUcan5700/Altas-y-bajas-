-- Solución para errores de columnas demasiado pequeñas en memoria_ram
-- Errores encontrados:
-- 1. TIPO_DDR: varchar(10) -> necesita varchar(50) para 'No especificado' (16 caracteres)
-- 2. numero_serie: varchar(15) -> necesita varchar(50) para 'CAT-RAM-1770922823870' (21 caracteres)
-- 3. numero_inventario: varchar(15) -> necesita varchar(50) para 'CAT-RAM-1770922823741' (21 caracteres)

-- Aumentar el tamaño de las columnas
ALTER TABLE `memoria_ram` 
MODIFY COLUMN `TIPO_DDR` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
MODIFY COLUMN `numero_serie` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
MODIFY COLUMN `numero_inventario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL;

-- Verificar los cambios
DESCRIBE memoria_ram;

-- Mostrar mensaje de éxito
SELECT 'Columnas actualizadas correctamente a varchar(50)' AS resultado;
