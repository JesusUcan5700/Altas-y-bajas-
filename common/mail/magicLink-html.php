<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
/* @var $loginUrl string */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-login {
            display: inline-block;
            padding: 15px 40px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🔑 Enlace de Acceso al Sistema de Inventario</h2>
    </div>
    
    <div class="content">
        <p><strong>Hola <?= Html::encode($authRequest->nombre_completo) ?>,</strong></p>
        
        <p>Has solicitado acceso al Sistema de Inventario. Haz clic en el botón de abajo para iniciar sesión:</p>
        
        <div class="button-container">
            <a href="<?= $loginUrl ?>" class="btn-login">🔓 Acceder al Sistema</a>
        </div>
        
        <div class="alert">
            <strong>⏰ Importante:</strong> Este enlace es válido solo por <strong>15 minutos</strong> y puede usarse una sola vez.
        </div>
        
        <div class="warning">
            <strong>🔒 Seguridad:</strong>
            <ul style="margin: 10px 0;">
                <li>No compartas este enlace con nadie</li>
                <li>Si no solicitaste este acceso, ignora este correo</li>
                <li>El enlace expirará automáticamente después de su uso</li>
            </ul>
        </div>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">
            Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br>
            <code style="background-color: #e9ecef; padding: 5px; display: block; margin-top: 10px; word-wrap: break-word;">
                <?= $loginUrl ?>
            </code>
        </p>
    </div>
    
    <div class="footer">
        <p>Sistema de Inventario - TecNM Valladolid</p>
        <p>Este correo fue enviado a: <?= Html::encode($authRequest->email) ?></p>
        <p>Por favor no responda a este mensaje</p>
    </div>
</body>
</html>
