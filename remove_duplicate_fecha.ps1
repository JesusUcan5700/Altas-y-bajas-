$filePath = "c:\wamp64\www\altas_bajas\frontend\views\site\historial-bajas.php"
$content = Get-Content $filePath -Raw

# Eliminar headers duplicados de Fecha de Baja
# Patrón: buscar dos <th>Fecha de Baja</th> consecutivos (con posibles espacios entre ellos)
while ($content -match '(<th>Fecha de Baja</th>)\s*<th>Fecha de Baja</th>') {
    $content = $content -replace '(<th>Fecha de Baja</th>)\s*<th>Fecha de Baja</th>', '$1'
}

# Eliminar celdas duplicadas de Fecha de Baja en el cuerpo
while ($content -match '(<td><?= Html::encode\(isset\(\$item->fecha_ultima_edicion\)[^<]+</td>)\s*<td><?= Html::encode\(isset\(\$item->fecha_ultima_edicion\)[^<]+</td>') {
    $content = $content -replace '(<td><?= Html::encode\(isset\(\$item->fecha_ultima_edicion\)[^<]+</td>)\s*<td><?= Html::encode\(isset\(\$item->fecha_ultima_edicion\)[^<]+</td>', '$1'
}

# Guardar el archivo
$content | Set-Content $filePath -NoNewline

Write-Host "Columnas duplicadas de Fecha de Baja eliminadas"
