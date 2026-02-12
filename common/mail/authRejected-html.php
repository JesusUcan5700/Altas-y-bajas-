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
            background-color: #dc3545;
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
        .reject-badge {
            background-color: #f8d7da;
            border: 2px solid #dc3545;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Solicitud de Acceso</h2>
    </div>
    
    <div class="content">
        <p><strong>Hola <?= Html::encode($authRequest->nombre_completo) ?>,</strong></p>
        
        <div class="reject-badge">
            <h3 style="margin: 0; color: #dc3545;">Solicitud No Aprobada</h3>
        </div>
        
        <p>Tu solicitud de acceso al Sistema de Inventario no ha sido aprobada en este momento.</p>
        
        <div class="info-box">
            <h4>ℹ️ ¿Qué puedes hacer?</h4>
            <p>Si consideras que deberías tener acceso al sistema, por favor contacta directamente con el 
            administrador del sistema en:</p>
            <p><strong>inventarioapoyoinformatico@valladolid.tecnm.mx</strong></p>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            El administrador podrá proporcionarte más información sobre los requisitos de acceso 
            o reconsiderar tu solicitud.
        </p>
    </div>
    
    <div class="footer">
        <p>Sistema de Inventario - TecNM Valladolid</p>
        <p>Este correo fue enviado a: <?= Html::encode($authRequest->email) ?></p>
        <p>Por favor no responda a este mensaje</p>
    </div>
</body>
</html>
