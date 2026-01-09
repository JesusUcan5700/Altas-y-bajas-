# Script para ejecutar el SQL de eliminación de duplicados
# Asegúrate de cambiar estos valores con tus credenciales reales

$mysqlPath = "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe"  # Ajusta la versión si es diferente
$database = "altas_bajas"  # Nombre de tu base de datos
$username = "root"  # Tu usuario de MySQL
$sqlFile = "c:\wamp64\www\altas_bajas\eliminar_duplicados.sql"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "ELIMINACIÓN DE DUPLICADOS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "ADVERTENCIA: Este script eliminará registros duplicados." -ForegroundColor Yellow
Write-Host "Asegúrate de haber hecho un backup de la base de datos." -ForegroundColor Yellow
Write-Host ""

$confirm = Read-Host "Deseas continuar? (S/N)"

if ($confirm -eq "S" -or $confirm -eq "s") {
    Write-Host ""
    Write-Host "Ejecutando script SQL..." -ForegroundColor Green
    
    # Ejecutar el script SQL
    Get-Content $sqlFile | & $mysqlPath -u $username -p $database
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Green
        Write-Host "Script ejecutado exitosamente!" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Red
        Write-Host "Error al ejecutar el script" -ForegroundColor Red
        Write-Host "========================================" -ForegroundColor Red
    }
} else {
    Write-Host ""
    Write-Host "Operación cancelada." -ForegroundColor Yellow
}
