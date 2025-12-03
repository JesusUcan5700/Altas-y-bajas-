use Codeception\Test\Unit;

<?php
/*
PSEUDOCÓDIGO / PLAN (detalle paso a paso):
1. Objetivo:
    - Crear pruebas que verifiquen cambios solicitados en la vista:
      a) El texto del botón de exportar debe ser "Exportar Resumen General del Inventario"
      b) El texto "(CSV)" debe desaparecer del HTML (no aparecer junto al botón).
      c) Debe existir en el código el nombre de archivo usado por la función JS para la exportación
          (ej. 'resumen_inventario.csv') para asegurar el formato esperado.

2. Estrategia de pruebas:
    - Las pruebas serán de tipo unitarias que validan el contenido fuente del archivo de vista PHP
      (frontend/views/site/stock.php) — ya que la lógica JS se encuentra inline en la vista.
    - Comprobar el archivo físicamente:
         - Leer el contenido del archivo.
         - Verificar que exista la cadena exacta para el nuevo texto del botón.
         - Verificar que no exista la cadena "(CSV)" en el contexto del botón de exportación.
         - Verificar que exista la cadena del nombre de archivo de exportación (resumen_inventario.csv).
    - Proveer mensajes claros en las aserciones para facilitar corrección en caso de fallo.

3. Estructura del test:
    - Clase de prueba que extiende Codeception\Test\Unit (coincidente con otros tests del proyecto).
    - Métodos:
         - testExportButtonLabelUpdated()
         - testExportFilenamePresent()
    - Cleanup: Ninguno (solo lectura).

4. Ubicación:
    - Archivo de prueba según petición: frontend/views/site/stockTest.php
    - Las rutas de los archivos se resuelven con __DIR__ para ser robustas.

5. Notas:
    - Estas pruebas no ejecutan el JS ni bootstrap; solo validan el contenido fuente para asegurar
      que el cambio solicitado está presente en el código fuente de la vista.
*/


class StockViewTest extends Unit
{
     /**
      * Ruta al archivo de vista que se desea comprobar.
      * El test asume que este archivo está en el mismo directorio que este test,
      * tal como solicitó: frontend/views/site/stock.php
      *
      * @return string
      */
     protected function viewFilePath(): string
     {
          return __DIR__ . DIRECTORY_SEPARATOR . 'stock.php';
     }

     public function testExportButtonLabelUpdated()
     {
          $file = $this->viewFilePath();
          $this->assertFileExists($file, "El archivo de vista esperado no existe en: $file");

          $content = file_get_contents($file);
          $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

          // 1) Verificar que el nuevo texto del botón exista
          $expectedButtonText = 'Exportar Resumen General del Inventario';
          $this->assertStringContainsString(
                $expectedButtonText,
                $content,
                "El botón de exportar no contiene el texto esperado: \"$expectedButtonText\"."
          );

          // 2) Verificar que no haya '(CSV)' junto al botón (o en el contenido global si se desea)
          $forbidden = '(CSV)';
          $this->assertStringNotContainsString(
                $forbidden,
                $content,
                "Se encontró la cadena \"$forbidden\" en la vista. El texto entre paréntesis debe eliminarse."
          );

          // 3) Verificar que el botón tenga el id esperado (para que los listeners JS lo encuentren)
          $this->assertStringContainsString(
                'id="btnExportar"',
                $content,
                "Se espera que el botón de exportar tenga el atributo id=\"btnExportar\"."
          );
     }

     public function testExportFilenamePresent()
     {
          $file = $this->viewFilePath();
          $this->assertFileExists($file, "El archivo de vista esperado no existe en: $file");

          $content = file_get_contents($file);
          $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

          // Verificar que el nombre de archivo usado en la exportación esté presente.
          // Se acepta 'resumen_inventario.csv' como nombre de archivo apropiado para exportar.
          $expectedFilename = 'resumen_inventario.csv';
          $this->assertStringContainsString(
                $expectedFilename,
                $content,
                "No se encontró el nombre de archivo de exportación esperado ('$expectedFilename') en la vista. Asegure que la función JS use este nombre para la descarga."
          );
     }
}

class StockExportTests extends \Codeception\Test\Unit
{
    protected function viewFilePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stock.php';
    }

    public function testExportButtonAndCsvRemoved()
    {
        $file = $this->viewFilePath();
        $this->assertFileExists($file, "El archivo de vista no existe en: $file");

        $content = file_get_contents($file);
        $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

        // a) Texto del botón de exportar
        $expectedButtonText = 'Exportar Resumen General del Inventario';
        $this->assertStringContainsString(
            $expectedButtonText,
            $content,
            "El botón de exportar debe contener el texto: \"$expectedButtonText\"."
        );

        // b) No debe contener '(CSV)'
        $forbidden = '(CSV)';
        $this->assertStringNotContainsString(
            $forbidden,
            $content,
            "Se encontró la cadena \"$forbidden\" en la vista. Debe eliminarse."
        );

        // c) Debe tener id="btnExportar"
        $this->assertStringContainsString(
            'id="btnExportar"',
            $content,
            'Se espera que el botón de exportar tenga el atributo id="btnExportar".'
        );
    }

    public function testXlsxLibraryAndFilename()
    {
        $file = $this->viewFilePath();
        $this->assertFileExists($file, "El archivo de vista no existe en: $file");

        $content = file_get_contents($file);
        $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

        // Verificar inclusión de SheetJS (XLSX)
        $this->assertTrue(
            stripos($content, 'cdn.sheetjs.com') !== false || stripos($content, 'xlsx.full.min.js') !== false,
            'No se encontró la inclusión de la librería SheetJS (XLSX). Busque "cdn.sheetjs.com" o "xlsx.full.min.js".'
        );

        // Verificar uso de XLSX.writeFile (descarga con SheetJS)
        $this->assertStringContainsString(
            'XLSX.writeFile',
            $content,
            'No se detectó la llamada a XLSX.writeFile en el JS de la vista. Es necesaria para generar el archivo Excel.'
        );

        // Verificar nombre de archivo .xlsx usado en la descarga
        $expectedFilename = 'Resumen General del Inventario.xlsx';
        $this->assertStringContainsString(
            $expectedFilename,
            $content,
            "No se encontró el nombre de archivo esperado ('$expectedFilename') en la vista JS."
        );
    }

    public function testExportStylingHints()
    {
        $file = $this->viewFilePath();
        $this->assertFileExists($file, "El archivo de vista no existe en: $file");

        $content = file_get_contents($file);
        $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

        // Detectar indicios de que se aplica estilo/formatos en las hojas (p. ej. .s, Object.assign, font bold)
        $hasStyleAssign = stripos($content, "Object.assign") !== false || stripos($content, "wsResumen['!cols']") !== false;
        $hasFontBold = stripos($content, "font: { bold") !== false || stripos($content, "'font':") !== false;
        $this->assertTrue(
            $hasStyleAssign && $hasFontBold,
            "Se espera que el JS de exportación incluya indicios de formato (Object.assign / ws...['!cols'] y font: { bold: true })."
        );
    }
}
class StockExcelExportTest extends \Codeception\Test\Unit
{
    protected function viewFilePath(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stock.php';
    }

    public function testExportButtonAndNoCsvLabel()
    {
        $file = $this->viewFilePath();
        $this->assertFileExists($file, "El archivo de vista esperado no existe en: $file");

        $content = file_get_contents($file);
        $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

        // Verificar que exista el botón de exportar con el id esperado
        $this->assertStringContainsString(
            'id="btnExportar"',
            $content,
            'Se espera que el botón de exportar tenga el atributo id="btnExportar".'
        );

        // Aceptar que el texto del botón incluya el resumen; no forzar 'Exportar' exacto si la vista usa otro wording.
        $this->assertStringContainsString(
            'Resumen General del Inventario',
            $content,
            'Se espera que el texto del botón o su título contenga "Resumen General del Inventario".'
        );

        // Verificar que no aparezca "(CSV)" en la vista (el sufijo CSV debe eliminarse)
        $this->assertStringNotContainsString(
            '(CSV)',
            $content,
            'Se encontró la cadena "(CSV)" en la vista. Debe eliminarse para indicar descarga en Excel o sin especificar CSV.'
        );
    }

    public function testSheetJsAndXlsxWriteFileForExcel()
    {
        $file = $this->viewFilePath();
        $this->assertFileExists($file, "El archivo de vista esperado no existe en: $file");

        $content = file_get_contents($file);
        $this->assertNotFalse($content, "No se pudo leer el contenido del archivo: $file");

        // Verificar inclusión de SheetJS (XLSX)
        $hasSheetJs = stripos($content, 'cdn.sheetjs.com') !== false || stripos($content, 'xlsx.full.min.js') !== false;
        $this->assertTrue(
            $hasSheetJs,
            'No se encontró la inclusión de la librería SheetJS (XLSX). Busque "cdn.sheetjs.com" o "xlsx.full.min.js".'
        );

        // Verificar uso de XLSX.writeFile (descarga .xlsx desde cliente)
        $this->assertTrue(
            stripos($content, 'XLSX.writeFile') !== false,
            'No se detectó la llamada a XLSX.writeFile en el JS de la vista. Es necesaria para generar/descargar el archivo Excel (.xlsx) desde el cliente.'
        );

        // Verificar que el nombre de archivo .xlsx esperado esté presente en el JS
        $expectedFilename = 'Resumen General del Inventario.xlsx';
        $this->assertStringContainsString(
            $expectedFilename,
            $content,
            "No se encontró el nombre de archivo esperado ('$expectedFilename') en la vista JS. Asegure que la función use este nombre para la descarga del Excel."
        );
    }
}