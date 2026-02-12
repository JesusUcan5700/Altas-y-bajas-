<?php

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
/* @var $loginUrl string */
?>

ENLACE DE ACCESO AL SISTEMA DE INVENTARIO
==========================================

Hola <?= $authRequest->nombre_completo ?>,

Has solicitado acceso al Sistema de Inventario.

Para iniciar sesión, haz clic en el siguiente enlace:

<?= $loginUrl ?>


IMPORTANTE:
-----------
* Este enlace es válido solo por 15 MINUTOS
* Puede usarse UNA SOLA VEZ
* No compartas este enlace con nadie
* Si no solicitaste este acceso, ignora este correo
* El enlace expirará automáticamente después de su uso


---
Sistema de Inventario - TecNM Valladolid
Este correo fue enviado a: <?= $authRequest->email ?>

Por favor no responda a este mensaje
