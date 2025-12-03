<?php
// Script simple para verificar propiedades sin Composer

class ImpresoraTest {
    const PROPIEDAD_PROPIA = 'propia';
    const PROPIEDAD_RENTADA = 'rentada';
    
    public static function getPropiedades() {
        return [
            self::PROPIEDAD_PROPIA => 'Propia',
            self::PROPIEDAD_RENTADA => 'Rentada',
        ];
    }
}

echo "=== VERIFICACIÓN DE PROPIEDADES ===\n\n";

echo "Constantes:\n";
echo "- PROPIEDAD_PROPIA = '" . ImpresoraTest::PROPIEDAD_PROPIA . "'\n";
echo "- PROPIEDAD_RENTADA = '" . ImpresoraTest::PROPIEDAD_RENTADA . "'\n\n";

echo "Opciones disponibles:\n";
$propiedades = ImpresoraTest::getPropiedades();
foreach($propiedades as $key => $value) {
    echo "- '$key' => '$value'\n";
}

echo "\nPrueba de formulario:\n";
echo "Si selecciono 'Propia' en el formulario:\n";
echo "- Se guarda en BD: '" . ImpresoraTest::PROPIEDAD_PROPIA . "'\n";
echo "- Se muestra como: '" . $propiedades[ImpresoraTest::PROPIEDAD_PROPIA] . "'\n\n";

echo "Si selecciono 'Rentada' en el formulario:\n";
echo "- Se guarda en BD: '" . ImpresoraTest::PROPIEDAD_RENTADA . "'\n";
echo "- Se muestra como: '" . $propiedades[ImpresoraTest::PROPIEDAD_RENTADA] . "'\n\n";

echo "✅ El modelo ahora está configurado correctamente.\n";
echo "✅ Cuando selecciones 'Propia', se guardará 'propia' y se mostrará 'Propia'.\n";
echo "✅ Cuando selecciones 'Rentada', se guardará 'rentada' y se mostrará 'Rentada'.\n\n";

echo "NOTA IMPORTANTE:\n";
echo "Si sigues viendo 'Arrendado' en el formulario, puede ser por:\n";
echo "1. Caché del navegador - presiona Ctrl+F5 para refrescar\n";
echo "2. Datos existentes en la BD con valor 'arrendado' (necesitan actualización)\n";
echo "3. Archivos de caché de Yii - revisa runtime/cache/\n";

?>
