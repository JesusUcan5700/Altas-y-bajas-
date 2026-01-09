-- Script para actualizar el CPU_ID del equipo 11
-- basándose en el texto del campo CPU

-- Paso 1: Ver el estado actual
SELECT 
    e.idEQUIPO,
    e.CPU,
    e.CPU_ID,
    p.idProcesador,
    p.MARCA,
    p.MODELO
FROM equipo e
LEFT JOIN procesadores p ON LOWER(CONCAT(p.MARCA, ' ', p.MODELO)) LIKE CONCAT('%', LOWER(TRIM(e.CPU)), '%')
WHERE e.idEQUIPO = 11;

-- Paso 2: Actualizar el CPU_ID basándose en el texto del CPU
-- Buscar el procesador que coincida con "AMD ryzen 5"
UPDATE equipo e
SET CPU_ID = (
    SELECT idProcesador 
    FROM procesadores 
    WHERE MARCA LIKE '%AMD%' 
    AND MODELO LIKE '%ryzen%5%'
    LIMIT 1
)
WHERE idEQUIPO = 11
AND CPU_ID IS NULL;

-- Paso 3: Verificar el resultado
SELECT 
    e.idEQUIPO,
    e.MARCA AS equipo_marca,
    e.MODELO AS equipo_modelo,
    e.CPU,
    e.CPU_ID,
    p.idProcesador,
    p.MARCA AS proc_marca,
    p.MODELO AS proc_modelo
FROM equipo e
LEFT JOIN procesadores p ON e.CPU_ID = p.idProcesador
WHERE e.idEQUIPO = 11;
