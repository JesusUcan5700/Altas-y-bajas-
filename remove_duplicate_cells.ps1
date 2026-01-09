$filePath = "c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php"
$content = Get-Content $filePath -Raw

# Eliminar celdas duplicadas de Estado al final de las filas
# Patrón: buscar líneas que tengan una celda de ubicación seguida de una celda de Estado duplicada
$content = $content -replace '(<td class="ubicacion-cell"[^>]*>.*?</td>)\s*<td><?= Html::encode\(\$item->Estado\) \?></td>', '$1'
$content = $content -replace '(<td><?= Html::encode\(\$item->ubicacion_edificio \. '' - '' \. \$item->ubicacion_detalle\) \?></td>)\s*<td><?= Html::encode\(\$item->Estado\) \?></td>', '$1'
$content = $content -replace '(<td><?= Html::encode\(\$item->ubicacion_edificio \. '' - '' \. \$item->ubicacion_detalle\) \?></td>)\s*<td><?= Html::encode\(\$item->ESTADO\) \?></td>', '$1'

# Guardar el archivo
$content | Set-Content $filePath -NoNewline

Write-Host "Celdas duplicadas eliminadas exitosamente"
