$filePath = "c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php"
$content = Get-Content $filePath -Raw

# Patrón para encontrar las secciones de descripción en los headers
$content = $content -replace '(<th>Descripción</th>)', '$1`r`n                                    <th>Fecha de Baja</th>'

# Patrón para encontrar las celdas de descripción en el cuerpo de las tablas
# Esto es más complejo porque necesitamos agregar después de la celda de descripción
$content = $content -replace '(<td[^>]*>.*?\$item->DESCRIPCION.*?</td>)(\s*<td)', '$1`r`n                                                <td><?= Html::encode(isset($item->fecha_ultima_edicion) && $item->fecha_ultima_edicion ? date(''Y-m-d'', strtotime($item->fecha_ultima_edicion)) : ''N/A'') ?></td>$2'

# Guardar el archivo
$content | Set-Content $filePath -NoNewline

Write-Host "Columnas de Fecha de Baja agregadas exitosamente"
