<?php
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \common\models\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Encabezado -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: white; margin: 0; font-size: 28px;">🔐 Recuperación de Contraseña</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">Sistema de Inventario de Altas y Bajas</p>
                        </td>
                    </tr>
                    
                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="font-size: 16px; color: #333; margin: 0 0 15px 0;">Hola <strong><?= Html::encode($user->username) ?></strong>,</p>
                            
                            <p style="font-size: 15px; color: #555; line-height: 1.6; margin: 0 0 20px 0;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta en el Sistema de Inventario.
                            </p>
                            
                            <p style="font-size: 15px; color: #555; line-height: 1.6; margin: 0 0 30px 0;">
                                Para crear una nueva contraseña, haz clic en el siguiente botón:
                            </p>
                            
                            <!-- Botón -->
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="<?= Html::encode($resetLink) ?>" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; font-size: 16px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            Restablecer mi Contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Enlace alternativo -->
                            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 30px 0;">
                                <p style="color: #666; font-size: 13px; margin: 0 0 10px 0;">
                                    <strong>Si no puedes hacer clic en el botón</strong>, copia y pega este enlace en tu navegador:
                                </p>
                                <p style="background: white; padding: 12px; border-radius: 5px; word-break: break-all; font-size: 12px; color: #667eea; border: 1px solid #e0e0e0; margin: 0;">
                                    <?= Html::encode($resetLink) ?>
                                </p>
                            </div>
                            
                            <!-- Nota de seguridad -->
                            <div style="border-top: 2px solid #e0e0e0; padding-top: 20px; margin-top: 30px;">
                                <p style="color: #999; font-size: 13px; line-height: 1.6; margin: 0;">
                                    <strong style="color: #667eea;">⏱️ Importante:</strong> Este enlace expirará en <strong>1 hora</strong> por seguridad.
                                </p>
                                <p style="color: #999; font-size: 13px; line-height: 1.6; margin: 10px 0 0 0;">
                                    <strong style="color: #667eea;">🔒 Nota de seguridad:</strong> Si no solicitaste este cambio, puedes ignorar este correo de forma segura. Tu contraseña actual no será modificada.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Pie de página -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="color: #999; font-size: 12px; margin: 0;">
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                            <p style="color: #999; font-size: 12px; margin: 10px 0 0 0;">
                                © <?= date('Y') ?> Sistema de Inventario - Universidad Autónoma de Yucatán
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
