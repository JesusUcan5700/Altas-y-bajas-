-- Script SQL para verificar el estado del equipo ID 11

-- Ver datos del equipo
SELECT 
    idEQUIPO,
    MARCA,
    MODELO,
    CPU,
    CPU_ID,
    CPU_DESC,
    NUM_INVENTARIO,
    Estado
FROM equipo 
WHERE idEQUIPO = 11;

-- Ver datos del procesador si existe CPU_ID
SELECT 
    p.idProcesador,
    p.MARCA,
    p.MODELO,
    p.FRECUENCIA_BASE,
    p.NUCLEOS,
    p.Estado,
    p.ubicacion_detalle
FROM procesadores p
WHERE p.idProcesador = (SELECT CPU_ID FROM equipo WHERE idEQUIPO = 11);

-- Ver TODOS los procesadores disponibles
SELECT 
    idProcesador,
    MARCA,
    MODELO,
    FRECUENCIA_BASE,
    Estado
FROM procesadores
ORDER BY idProcesador;
