$filePath = "c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php"
$content = Get-Content $filePath -Raw

# Para tablas sin Descripción, agregar después de Ubicación
# Patrón para headers
$content = $content -replace '(<th>Ubicación</th>)(\s*</tr>)', "`$1`r`n                                    <th>Fecha de Baja</th>`$2"

# Patrón para celdas de datos - agregar después de ubicacion_edificio . ' - ' . ubicacion_detalle
# Solo si no hay ya una celda de Fecha de Baja después
$pattern = '(<td><?= Html::encode\(\$item->ubicacion_edificio \. '' - '' \. \$item->ubicacion_detalle\) \?></td>)(\s*</tr>)'
$replacement = "`$1`r`n                                                <td><?= Html::encode(isset(`$item->fecha_ultima_edicion) && `$item->fecha_ultima_edicion ? date('Y-m-d', strtotime(`$item->fecha_ultima_edicion)) : 'N/A') ?></td>`$2"
$content = $content -replace $pattern, $replacement

# Guardar el archivo
$content | Set-Content $filePath -NoNewline

Write-Host "Columnas de Fecha de Baja agregadas para tablas sin Descripción"
