-- =====================================================
-- Script para insertar dispositivos de prueba con Estado BAJA
-- Fecha: 2026-01-08
-- Propósito: Probar funcionalidad de reciclaje de piezas
-- =====================================================

-- EQUIPOS DE CÓMPUTO (tabla: equipo)
-- Campos obligatorios: CPU, DD, RAM, MARCA, MODELO, NUM_SERIE, NUM_INVENTARIO, EMISION_INVENTARIO, tipoequipo
INSERT INTO `equipo` (`CPU`, `DD`, `RAM`, `MARCA`, `MODELO`, `NUM_SERIE`, `NUM_INVENTARIO`, `EMISION_INVENTARIO`, `Estado`, `tipoequipo`, `ubicacion_edificio`, `ubicacion_detalle`, `descripcion`, `ultimo_editor`)
VALUES
('Intel Core i5-9500', '500GB HDD', '8GB DDR4', 'Dell', 'OptiPlex 3070', 'SN-DELL-001', 'INV-2025-001', '2024-01-15', 'BAJA', 'Desktop', 'Edificio A', 'Almacén de Baja', 'Tarjeta madre quemada - RAM y DD recuperables', 'Juan Pérez'),
('Intel Core i7-7700', '256GB SSD', '16GB DDR4', 'HP', 'EliteDesk 800 G3', 'SN-HP-002', 'INV-2025-002', '2024-02-20', 'BAJA', 'Desktop', 'Edificio A', 'Almacén de Baja', 'Fuente defectuosa - Procesador, RAM y SSD OK', 'María García'),
('Intel Core i3-8100', '1TB HDD', '4GB DDR4', 'Lenovo', 'ThinkCentre M720', 'SN-LEN-003', 'INV-2025-003', '2023-11-10', 'BAJA', 'Desktop', 'Edificio A', 'Almacén de Baja', 'Obsoleto - Equipo completo funcional', 'Carlos López'),
('Intel Core i5-8400', '500GB SSD', '8GB DDR4', 'Acer', 'Veriton X2665G', 'SN-ACER-004', 'INV-2024-045', '2023-10-05', 'BAJA', 'Desktop', 'Edificio A', 'Almacén de Baja', 'Actualización - Todos componentes operativos', 'Ana Martínez'),
('AMD Ryzen 5 3500U', '512GB SSD', '12GB DDR4', 'ASUS', 'VivoBook 15', 'SN-ASUS-005', 'INV-2024-078', '2023-09-25', 'BAJA', 'Laptop', 'Edificio A', 'Almacén de Baja', 'Pantalla rota - Componentes internos excelentes', 'Roberto Sánchez'),
('Intel Core i7-8650U', '256GB SSD', '16GB DDR4', 'Dell', 'Latitude 5490', 'SN-DELL-006', 'INV-2023-120', '2022-08-30', 'BAJA', 'Laptop', 'Edificio A', 'Almacén de Baja', 'Daño por líquido - RAM y SSD recuperables', 'Laura Ramírez'),
('Intel Core i5-8500', '1TB HDD', '8GB DDR4', 'HP', 'ProDesk 600 G4', 'SN-HP-007', 'INV-2023-089', '2022-12-01', 'BAJA', 'Desktop', 'Edificio A', 'Almacén de Baja', 'Motherboard defectuosa - Fuente y DD OK', 'Pedro Torres');

-- MONITORES (tabla: monitor)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `monitor` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `TAMANIO`, `TIPO_PANTALLA`, `RESOLUCION`, `ENTRADAS_VIDEO`, `ESTADO`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('Samsung', 'S24F350', 'MON-SAM-001', 'MON-2025-001', '2024-12-10', '24"', 'LED', '1920x1080', 'HDMI/VGA', 'BAJA', 'Líneas en pantalla - Fuente de poder funciona', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('LG', '24MK430H', 'MON-LG-002', 'MON-2025-002', '2024-11-15', '24"', 'IPS', '1920x1080', 'HDMI', 'BAJA', 'No enciende - Panel posiblemente recuperable', 'Edificio A', 'Almacén de Baja', 'María García'),
('Dell', 'P2219H', 'MON-DELL-003', 'MON-2024-056', '2023-10-20', '22"', 'LED', '1920x1080', 'DisplayPort/HDMI', 'BAJA', 'Obsoleto - Monitor funcional', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('HP', 'V194', 'MON-HP-004', 'MON-2024-034', '2023-09-15', '19"', 'LED', '1366x768', 'VGA', 'BAJA', 'Actualización - Funcionando correctamente', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('AOC', 'E2270SWHN', 'MON-AOC-005', 'MON-2023-112', '2022-12-05', '22"', 'LED', '1920x1080', 'HDMI/VGA', 'BAJA', 'Botones no responden - Imagen perfecta', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez'),
('BenQ', 'GW2480', 'MON-BENQ-006', 'MON-2023-098', '2022-11-25', '24"', 'IPS', '1920x1080', 'HDMI/DisplayPort', 'BAJA', 'Backlight intermitente - Componentes recuperables', 'Edificio A', 'Almacén de Baja', 'Laura Ramírez');

-- IMPRESORAS (tabla: impresora)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `impresora` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `TIPO`, `CONEXION`, `Estado`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('HP', 'LaserJet P1102w', 'IMP-HP-001', 'IMP-2025-001', '2024-12-08', 'Láser', 'USB/WiFi', 'BAJA', 'Unidad fusora dañada - Tóner y componentes recuperables', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('Canon', 'PIXMA G3110', 'IMP-CAN-002', 'IMP-2025-002', '2024-11-20', 'Inyección de tinta', 'USB/WiFi', 'BAJA', 'Sistema de tinta obstruido - Tarjeta lógica funcional', 'Edificio A', 'Almacén de Baja', 'María García'),
('Epson', 'L3150', 'IMP-EPS-003', 'IMP-2024-067', '2023-10-12', 'EcoTank', 'USB/WiFi', 'BAJA', 'Error de cabezal - Fuente de poder OK', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('Brother', 'DCP-L2540DW', 'IMP-BRO-004', 'IMP-2024-045', '2023-09-30', 'Láser Multifuncional', 'USB/WiFi', 'BAJA', 'Escáner no funciona - Impresión laser funcional', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('Samsung', 'Xpress M2020W', 'IMP-SAM-005', 'IMP-2023-089', '2022-12-02', 'Láser', 'USB/WiFi', 'BAJA', 'Obsoleta - Funcionando perfectamente', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez');

-- TELEFONÍA (tabla: telefonia)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `telefonia` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `TIPO`, `CARACTERISTICAS`, `Estado`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('Cisco', 'IP Phone 7940', 'TEL-CIS-001', 'TEL-2025-001', '2024-12-18', 'Teléfono IP', 'Display LCD, 2 líneas, PoE', 'BAJA', 'Actualización sistema - Funcional', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('Grandstream', 'GXP1628', 'TEL-GRA-002', 'TEL-2025-002', '2024-11-28', 'Teléfono IP', '2 líneas, PoE, LCD', 'BAJA', 'Pantalla dañada - Circuitos internos OK', 'Edificio A', 'Almacén de Baja', 'María García'),
('Yealink', 'T21P E2', 'TEL-YEA-003', 'TEL-2024-078', '2023-10-22', 'Teléfono IP', '2 líneas, PoE', 'BAJA', 'No registra en red - Hardware en buen estado', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('Panasonic', 'KX-TS500', 'TEL-PAN-004', 'TEL-2024-056', '2023-09-18', 'Teléfono Analógico', 'Altavoz, identificador', 'BAJA', 'Migración a IP - Totalmente funcional', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('Polycom', 'VVX 301', 'TEL-POL-005', 'TEL-2023-134', '2022-12-12', 'Teléfono IP', '6 líneas, PoE, Gigabit', 'BAJA', 'Botones no responden - Audio y red OK', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez');

-- VIDEO VIGILANCIA (tabla: videovigilancia)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `videovigilancia` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `TIPO`, `RESOLUCION`, `Estado`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('Hikvision', 'DS-2CD2042WD-I', 'CAM-HIK-001', 'CAM-2025-001', '2024-12-22', 'IP Bullet', '4MP (2688x1520)', 'BAJA', 'Sensor dañado - Lente y carcasa en buen estado', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('Dahua', 'IPC-HFW1230S', 'CAM-DAH-002', 'CAM-2025-002', '2024-11-30', 'IP Bullet', '2MP (1920x1080)', 'BAJA', 'No transmite imagen - Fuente PoE funcional', 'Edificio A', 'Almacén de Baja', 'María García'),
('Uniview', 'IPC322SR3-VSF28', 'CAM-UNI-003', 'CAM-2024-089', '2023-10-15', 'IP Dome', '2MP (1920x1080)', 'BAJA', 'Actualización de sistema - Cámara operativa', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('Axis', 'M1065-L', 'CAM-AXI-004', 'CAM-2024-067', '2023-09-20', 'IP Box', '2MP (1920x1080)', 'BAJA', 'Obsoleta - Funcionando bien', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('CP Plus', 'CP-UNC-TA10L2-V3', 'CAM-CPP-005', 'CAM-2023-145', '2022-12-15', 'IP Turret', '1MP (1280x720)', 'BAJA', 'Baja resolución - Compatible con grabadores viejos', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez');

-- CONECTIVIDAD (tabla: conectividad)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `conectividad` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `TIPO`, `CARACTERISTICAS`, `Estado`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('TP-Link', 'TL-WR841N', 'NET-TPL-001', 'NET-2025-001', '2024-12-20', 'Router WiFi', 'N300, 4 puertos LAN', 'BAJA', 'Actualización de red - Funcional pero limitado', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('Cisco', 'SG200-08', 'NET-CIS-002', 'NET-2025-002', '2024-11-22', 'Switch Gestionable', '8 puertos Gigabit', 'BAJA', 'Puerto 5 dañado - 7 puertos operativos', 'Edificio A', 'Almacén de Baja', 'María García'),
('D-Link', 'DGS-1008G', 'NET-DLI-003', 'NET-2024-078', '2023-10-10', 'Switch', '8 puertos Gigabit', 'BAJA', 'Fuente de poder quemada - Circuitos recuperables', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('Ubiquiti', 'UniFi AP-AC-Lite', 'NET-UBI-004', 'NET-2024-056', '2023-09-25', 'Access Point', 'AC1200, PoE', 'BAJA', 'No enciende - Antenas en buen estado', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('Netgear', 'GS108', 'NET-NET-005', 'NET-2023-112', '2022-12-18', 'Switch', '8 puertos Gigabit', 'BAJA', 'Sobrecalentamiento - Algunos componentes OK', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez');

-- BATERÍAS (tabla: bateria)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `bateria` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `TIPO`, `CAPACIDAD`, `VOLTAJE`, `Estado`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('APC', 'RBC7', 'BAT-APC-001', 'BAT-2025-001', '2024-12-25', 'Sellada de Plomo-Ácido', '12V 7Ah', '12V', 'BAJA', 'No mantiene carga - Fin de vida útil', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('CSB', 'GP1272', 'BAT-CSB-002', 'BAT-2025-002', '2024-11-18', 'AGM', '12V 7.2Ah', '12V', 'BAJA', 'Descarga rápida - Requiere reciclaje adecuado', 'Edificio A', 'Almacén de Baja', 'María García'),
('Yuasa', 'NP18-12', 'BAT-YUA-003', 'BAT-2024-089', '2023-10-08', 'Sellada de Plomo-Ácido', '12V 18Ah', '12V', 'BAJA', 'Sulfatada - Material reciclable', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('Panasonic', 'LC-R127R2P', 'BAT-PAN-004', 'BAT-2024-067', '2023-09-15', 'Plomo-Ácido Regulada', '12V 7.2Ah', '12V', 'BAJA', 'Vencida - Reciclaje programado', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('Interstate', 'BSL1116', 'BAT-INT-005', 'BAT-2023-134', '2022-12-10', 'AGM', '12V 35Ah', '12V', 'BAJA', 'Capacidad reducida - Material recuperable', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez');

-- NO BREAK (UPS) (tabla: nobreak)
-- Campos obligatorios: MARCA, MODELO
INSERT INTO `nobreak` (`MARCA`, `MODELO`, `NUMERO_SERIE`, `NUMERO_INVENTARIO`, `EMISION_INVENTARIO`, `CAPACIDAD`, `Estado`, `DESCRIPCION`, `ubicacion_edificio`, `ubicacion_detalle`, `ultimo_editor`)
VALUES
('APC', 'Back-UPS ES 700', 'UPS-APC-001', 'UPS-2025-001', '2024-12-28', '700VA/405W', 'BAJA', 'Batería agotada no reemplazable - Circuitos internos operativos', 'Edificio A', 'Almacén de Baja', 'Juan Pérez'),
('Tripp Lite', 'SMART1500LCD', 'UPS-TRI-002', 'UPS-2025-002', '2024-11-24', '1500VA/900W', 'BAJA', 'Display dañado - UPS funcional, solo display', 'Edificio A', 'Almacén de Baja', 'María García'),
('CyberPower', 'CP1000AVRLCD', 'UPS-CYB-003', 'UPS-2024-078', '2023-10-18', '1000VA/600W', 'BAJA', 'No carga batería - Módulo de carga defectuoso', 'Edificio A', 'Almacén de Baja', 'Carlos López'),
('Forza', 'SL-761UL', 'UPS-FOR-004', 'UPS-2024-056', '2023-09-22', '750VA/375W', 'BAJA', 'Actualización de equipos - Funcionando correctamente', 'Edificio A', 'Almacén de Baja', 'Ana Martínez'),
('Sola Basic', 'ISB XII 600', 'UPS-SOL-005', 'UPS-2023-145', '2022-12-20', '600VA/300W', 'BAJA', 'Obsoleto - Regulador funcional, batería muerta', 'Edificio A', 'Almacén de Baja', 'Roberto Sánchez');

-- =====================================================
-- Resumen de dispositivos insertados:
-- - 7 Equipos de Cómputo
-- - 6 Monitores
-- - 5 Impresoras
-- - 5 Teléfonos
-- - 5 Cámaras de Video Vigilancia
-- - 5 Dispositivos de Conectividad
-- - 5 Baterías
-- - 5 No Break
-- TOTAL: 43 dispositivos con estado BAJA
-- =====================================================

-- Para verificar los registros insertados, ejecuta:
SELECT 'Equipos' as Categoria, COUNT(*) as Total FROM equipo WHERE Estado = 'BAJA'
UNION ALL
SELECT 'Monitores', COUNT(*) FROM monitor WHERE ESTADO = 'BAJA'
UNION ALL
SELECT 'Impresoras', COUNT(*) FROM impresora WHERE Estado = 'BAJA'
UNION ALL
SELECT 'Telefonía', COUNT(*) FROM telefonia WHERE Estado = 'BAJA'
UNION ALL
SELECT 'VideoVigilancia', COUNT(*) FROM videovigilancia WHERE Estado = 'BAJA'
UNION ALL
SELECT 'Conectividad', COUNT(*) FROM conectividad WHERE Estado = 'BAJA'
UNION ALL
SELECT 'Baterías', COUNT(*) FROM bateria WHERE Estado = 'BAJA'
UNION ALL
SELECT 'NoBreak', COUNT(*) FROM nobreak WHERE Estado = 'BAJA';
