<?php
/** @var \yii\web\View $this */
/** @var \common\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
============================================
RECUPERACIÓN DE CONTRASEÑA
Sistema de Inventario de Altas y Bajas
============================================

Hola <?= $user->username ?>,

Recibimos una solicitud para restablecer la contraseña de tu cuenta.

Para crear una nueva contraseña, copia y pega el siguiente enlace en tu navegador:

<?= $resetLink ?>


IMPORTANTE:
• Este enlace expirará en 1 hora por seguridad
• Si no solicitaste este cambio, ignora este correo
• Tu contraseña actual seguirá siendo válida hasta que completes el proceso

---
Este es un correo automático, por favor no respondas.

© <?= date('Y') ?> Sistema de Inventario - Universidad Autónoma de Yucatán
