<?php

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
?>

SOLICITUD DE ACCESO
===================

Hola <?= $authRequest->nombre_completo ?>,

Tu solicitud de acceso al Sistema de Inventario no ha sido aprobada en este momento.

¿QUÉ PUEDES HACER?
------------------
Si consideras que deberías tener acceso al sistema, por favor contacta 
directamente con el administrador del sistema en:

inventarioapoyoinformatico@valladolid.tecnm.mx

El administrador podrá proporcionarte más información sobre los requisitos 
de acceso o reconsiderar tu solicitud.

---
Sistema de Inventario - TecNM Valladolid
Este correo fue enviado a: <?= $authRequest->email ?>

Por favor no responda a este mensaje
