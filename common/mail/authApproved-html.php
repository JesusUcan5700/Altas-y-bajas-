<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
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
            padding: 30px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .success-badge {
            background-color: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn-access {
            display: inline-block;
            padding: 15px 40px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .instructions {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>✅ ¡Acceso Aprobado!</h2>
    </div>
    
    <div class="content">
        <p><strong>Hola <?= Html::encode($authRequest->nombre_completo) ?>,</strong></p>
        
        <div class="success-badge">
            <h3 style="margin: 0; color: #28a745;">🎉 Tu solicitud ha sido aprobada</h3>
        </div>
        
        <p>Tu solicitud de acceso al Sistema de Inventario ha sido <strong>APROBADA</strong> por el administrador.</p>
        
        <div class="instructions">
            <h4>📋 Instrucciones para acceder al sistema:</h4>
            <ol>
                <li>Visita la página de acceso del sistema</li>
                <li>Ingresa tu correo electrónico: <strong><?= Html::encode($authRequest->email) ?></strong></li>
                <li>Recibirás un enlace mágico de acceso temporal en tu correo</li>
                <li>Haz clic en el enlace para ingresar al sistema</li>
            </ol>
        </div>
        
        <div class="button-container">
            <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['site/auth-login']) ?>" class="btn-access">
                🔑 Ir a la Página de Acceso
            </a>
        </div>
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            <strong>Nota importante:</strong> Cada vez que quieras acceder al sistema, deberás 
            solicitar un nuevo enlace de acceso temporal. Esto garantiza la seguridad de tus datos.
        </p>
    </div>
    
    <div class="footer">
        <p>Sistema de Inventario - TecNM Valladolid</p>
        <p>Este correo fue enviado a: <?= Html::encode($authRequest->email) ?></p>
        <p>Por favor no responda a este mensaje</p>
    </div>
</body>
</html>
