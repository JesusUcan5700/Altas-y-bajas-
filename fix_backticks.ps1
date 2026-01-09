$filePath = "c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php"
$content = Get-Content $filePath -Raw

# Primero, limpiar los backticks literales que se agregaron
$content = $content -replace '`r`n', "`r`n"

# Guardar el archivo
$content | Set-Content $filePath -NoNewline

Write-Host "Archivo limpiado exitosamente"
