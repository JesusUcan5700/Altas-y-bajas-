-- Solución para el error de columna TIPO_DDR demasiado pequeña
-- El error ocurre porque la columna tiene varchar(10) pero se intenta insertar 'No especificado' (16 caracteres)

-- Aumentar el tamaño de la columna TIPO_DDR a varchar(50)
ALTER TABLE `memoria_ram` 
MODIFY COLUMN `TIPO_DDR` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

-- Verificar el cambio
DESCRIBE memoria_ram;

-- Mostrar mensaje de éxito
SELECT 'Columna TIPO_DDR actualizada correctamente a varchar(50)' AS resultado;
