<?php

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
?>

¡ACCESO APROBADO!
=================

Hola <?= $authRequest->nombre_completo ?>,

Tu solicitud de acceso al Sistema de Inventario ha sido APROBADA por el administrador.

INSTRUCCIONES PARA ACCEDER AL SISTEMA:
---------------------------------------
1. Visita la página de acceso del sistema
2. Ingresa tu correo electrónico: <?= $authRequest->email ?>

3. Recibirás un enlace mágico de acceso temporal en tu correo
4. Haz clic en el enlace para ingresar al sistema

ACCEDER AHORA:
--------------
<?= Yii::$app->urlManager->createAbsoluteUrl(['site/auth-login']) ?>


Nota importante: Cada vez que quieras acceder al sistema, deberás solicitar 
un nuevo enlace de acceso temporal. Esto garantiza la seguridad de tus datos.

---
Sistema de Inventario - TecNM Valladolid
Este correo fue enviado a: <?= $authRequest->email ?>

Por favor no responda a este mensaje
