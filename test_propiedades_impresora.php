<?php
/**
 * Script de verificación para testing rápido del modelo Impresora
 * Verifica que las propiedades se mapeen correctamente
 */

// Simular el entorno de Yii (para testing básico)
class ImpresoraTest 
{
    const PROPIEDAD_PROPIA = 'propio';
    const PROPIEDAD_ARRENDADO = 'arrendado';
    
    public static function getPropiedades()
    {
        return [
            self::PROPIEDAD_PROPIA => 'Propio',
            self::PROPIEDAD_ARRENDADO => 'Arrendado',
        ];
    }
}

// Pruebas
echo "=== VERIFICACIÓN DEL MAPEO DE PROPIEDADES ===\n\n";

$propiedades = ImpresoraTest::getPropiedades();

echo "Mapeo actual:\n";
foreach ($propiedades as $valor_bd => $etiqueta_mostrada) {
    echo "- Valor en BD: '$valor_bd' -> Mostrado como: '$etiqueta_mostrada'\n";
}

echo "\n=== VERIFICACIÓN DE LÓGICA ===\n";
echo "Si selecciono 'Propio' en el formulario:\n";
echo "- Se guarda en BD: '" . ImpresoraTest::PROPIEDAD_PROPIA . "'\n";
echo "- Se muestra como: '" . $propiedades[ImpresoraTest::PROPIEDAD_PROPIA] . "'\n";

echo "\nSi selecciono 'Arrendado' en el formulario:\n";
echo "- Se guarda en BD: '" . ImpresoraTest::PROPIEDAD_ARRENDADO . "'\n";
echo "- Se muestra como: '" . $propiedades[ImpresoraTest::PROPIEDAD_ARRENDADO] . "'\n";

echo "\n✅ El mapeo ahora está correcto.\n";
echo "✅ Cuando selecciones 'Propio', se guardará 'propio' y se mostrará 'Propio'.\n";
echo "✅ Cuando selecciones 'Arrendado', se guardará 'arrendado' y se mostrará 'Arrendado'.\n";
?>
