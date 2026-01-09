$filePath = "c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php"
$content = Get-Content $filePath -Raw

# Actualizar todos los colspans sumando 1
for ($i = 25; $i -ge 9; $i--) {
    $newColspan = $i + 1
    $content = $content -replace "colspan=`"$i`"", "colspan=`"$newColspan`""
}

# Guardar el archivo
$content | Set-Content $filePath -NoNewline

Write-Host "Colspans actualizados exitosamente"
