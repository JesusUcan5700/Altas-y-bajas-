<?php
$pdo = new PDO('mysql:host=localhost;dbname=altas_bajas;charset=utf8', 'root', '');

echo "Estructura de la tabla monitor:\n";
$stmt = $pdo->query('DESCRIBE monitor');
while($row = $stmt->fetch()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . "\n";
}

echo "\nCampos en tabla equipo relacionados con monitor:\n";
$stmt = $pdo->query("SHOW COLUMNS FROM equipo LIKE '%MONITOR%'");
while($row = $stmt->fetch()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . "\n";
}
?>