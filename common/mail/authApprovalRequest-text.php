<?php

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
/* @var $approveUrl string */
/* @var $rejectUrl string */
?>

NUEVA SOLICITUD DE ACCESO AL SISTEMA DE INVENTARIO
==================================================

Hola Administrador,

Se ha recibido una nueva solicitud de acceso al Sistema de Inventario.

DETALLES DE LA SOLICITUD:
--------------------------
Email: <?= $authRequest->email ?>

Nombre: <?= $authRequest->nombre_completo ?>

<?php if ($authRequest->departamento): ?>
Departamento: <?= $authRequest->departamento ?>

<?php endif; ?>
Fecha de Solicitud: <?= Yii::$app->formatter->asDatetime($authRequest->created_at) ?>


ACCIONES DISPONIBLES:
---------------------

Para APROBAR la solicitud, visite:
<?= $approveUrl ?>


Para RECHAZAR la solicitud, visite:
<?= $rejectUrl ?>


Nota: Al aprobar, el usuario podrá solicitar enlaces de acceso temporal enviados a su correo.

---
Este es un correo automático del Sistema de Inventario TecNM Valladolid
Por favor no responda a este mensaje
