<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $authRequest common\models\AuthRequest */
/* @var $approveUrl string */
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
            background-color: #007bff;
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
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }
        .btn-approve {
            background-color: #28a745;
        }
        .btn-approve:hover {
            background-color: #218838;
        }
        .btn-reject {
            background-color: #dc3545;
        }
        .btn-reject:hover {
            background-color: #c82333;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🔐 Nueva Solicitud de Acceso al Sistema</h2>
    </div>
    
    <div class="content">
        <p><strong>Hola Administrador,</strong></p>
        
        <p>Se ha recibido una nueva solicitud de acceso al Sistema de Inventario.</p>
        
        <div class="info-box">
            <h3>Detalles de la Solicitud:</h3>
            <ul style="list-style: none; padding: 0;">
                <li><strong>📧 Email:</strong> <?= Html::encode($authRequest->email) ?></li>
                <li><strong>👤 Nombre:</strong> <?= Html::encode($authRequest->nombre_completo) ?></li>
                <?php if ($authRequest->departamento): ?>
                <li><strong>🏢 Departamento:</strong> <?= Html::encode($authRequest->departamento) ?></li>
                <?php endif; ?>
                <li><strong>📅 Fecha de Solicitud:</strong> <?= Yii::$app->formatter->asDatetime($authRequest->created_at) ?></li>
            </ul>
        </div>
        
        <p>Por favor, haga clic en el siguiente enlace para revisar y tomar una decisión:</p>
        
        <div class="button-container">
            <a href="<?= $approveUrl ?>" class="btn btn-approve" style="color: white;">🔍 Revisar Solicitud</a>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            <strong>⚠️ Importante:</strong> Al hacer clic se abrirá una página donde podrá <strong>aprobar o rechazar</strong> la solicitud.
            La aprobación NO es automática, usted debe confirmar su decisión.
        </p>
        <p style="color: #666; font-size: 14px;">
            <strong>Nota:</strong> Al aprobar, el usuario podrá iniciar sesión en el sistema.
        </p>
    </div>
    
    <div class="footer">
        <p>Este es un correo automático del Sistema de Inventario TecNM Valladolid</p>
        <p>Por favor no responda a este mensaje</p>
    </div>
</body>
</html>
