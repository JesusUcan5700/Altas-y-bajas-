<?php

require_once 'config/conexion.php';
require_once 'funciones/filtrar_equipos.php';

// Verificar permisos
// ...

// Obtener filtros de la URL
$filtros = [
    'estado' => $_GET['estado'] ?? '',
    'departamento' => $_GET['departamento'] ?? '',
    'fecha_desde' => $_GET['fecha_desde'] ?? '',
    'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
];

// Obtener datos
$equipos = obtenerEquiposBajasDanados($filtros);

// Determinar el formato de exportación
$formato = $_GET['formato'] ?? 'pdf';

if ($formato === 'pdf') {
    // Requiere librería FPDF o similar
    require_once 'vendor/fpdf/fpdf.php';
    
    // Código para generar PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Historial de Equipos de Baja o Dañados', 0, 1, 'C');
    
    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(10, 7, 'ID', 1);
    $pdf->Cell(40, 7, 'Equipo', 1);
    $pdf->Cell(30, 7, 'Serie', 1);
    $pdf->Cell(20, 7, 'Estado', 1);
    $pdf->Cell(30, 7, 'Fecha Baja', 1);
    $pdf->Cell(60, 7, 'Motivo', 1);
    $pdf->Ln();
    
    // Datos
    $pdf->SetFont('Arial', '', 10);
    foreach ($equipos as $equipo) {
        $pdf->Cell(10, 6, $equipo['id'], 1);
        $pdf->Cell(40, 6, utf8_decode(strtoupper($equipo['nombre_equipo'])), 1);
        $pdf->Cell(30, 6, strtoupper($equipo['serie']), 1);
        $pdf->Cell(20, 6, strtoupper($equipo['estado']), 1);
        $pdf->Cell(30, 6, strtoupper($equipo['fecha_baja']), 1);
        $pdf->Cell(60, 6, utf8_decode(strtoupper(substr($equipo['motivo_baja'], 0, 30))), 1);
        $pdf->Ln();
    }
    
    // Generar salida
    $pdf->Output('EQUIPOS_BAJA_DANADOS.pdf', 'D');
    
} elseif ($formato === 'excel') {
    // Requiere librería PhpSpreadsheet o similar
    require_once 'vendor/autoload.php';
    
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Título
    $sheet->setCellValue('A1', 'HISTORIAL DE EQUIPOS DE BAJA O DAÑADOS');
    $sheet->mergeCells('A1:G1');
    
    // Encabezados
    $sheet->setCellValue('A2', 'ID');
    $sheet->setCellValue('B2', 'EQUIPO');
    $sheet->setCellValue('C2', 'SERIE');
    $sheet->setCellValue('D2', 'MODELO');
    $sheet->setCellValue('E2', 'ESTADO');
    $sheet->setCellValue('F2', 'FECHA BAJA');
    $sheet->setCellValue('G2', 'MOTIVO');
    $sheet->setCellValue('H2', 'RESPONSABLE');
    $sheet->setCellValue('I2', 'DEPARTAMENTO');
    
    // Datos
    $row = 3;
    foreach ($equipos as $equipo) {
        $sheet->setCellValue('A' . $row, $equipo['id']);
        $sheet->setCellValue('B' . $row, strtoupper($equipo['nombre_equipo']));
        $sheet->setCellValue('C' . $row, strtoupper($equipo['serie']));
        $sheet->setCellValue('D' . $row, strtoupper($equipo['modelo']));
        $sheet->setCellValue('E' . $row, strtoupper($equipo['estado']));
        $sheet->setCellValue('F' . $row, strtoupper($equipo['fecha_baja']));
        $sheet->setCellValue('G' . $row, strtoupper($equipo['motivo_baja']));
        $sheet->setCellValue('H' . $row, strtoupper($equipo['responsable']));
        $sheet->setCellValue('I' . $row, strtoupper($equipo['departamento']));
        $row++;
    }
    
    // Generar archivo
    $writer = new Xlsx($spreadsheet);
    
    // Encabezados para descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="EQUIPOS_BAJA_DANADOS.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
}

exit();
?>