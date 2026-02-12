<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\AccessRequestForm */

$this->title = 'Solicitar Acceso al Sistema';

$this->registerCss("
    .request-access-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
    }
    
    .request-access-box {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        padding: 40px;
        max-width: 500px;
        width: 100%;
    }
    
    .request-access-box h1 {
        color: #667eea;
        margin-bottom: 10px;
        font-size: 28px;
        text-align: center;
    }
    
    .request-access-box .subtitle {
        color: #666;
        margin-bottom: 30px;
        text-align: center;
        font-size: 14px;
    }
    
    .icon-header {
        text-align: center;
        font-size: 60px;
        margin-bottom: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .btn-request {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        color: white;
        transition: transform 0.2s;
    }
    
    .btn-request:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    .back-to-login {
        text-align: center;
        margin-top: 20px;
    }
    
    .back-to-login a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }
    
    .back-to-login a:hover {
        text-decoration: underline;
    }
    
    .info-box {
        background-color: #e7f3ff;
        border-left: 4px solid #2196F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
");
?>

<div class="request-access-page">
    <div class="request-access-box">
        <div class="icon-header">🔐</div>
        
        <h1><?= Html::encode($this->title) ?></h1>
        <p class="subtitle">Complete el formulario para solicitar autorización</p>
        
        <div class="info-box">
            <strong>ℹ️ Proceso de Autorización:</strong>
            <ol style="margin: 10px 0 0 0; padding-left: 20px; font-size: 13px;">
                <li>Complete sus datos (puede usar correo personal o institucional)</li>
                <li>Se enviará una solicitud al administrador</li>
                <li>Recibirá un correo cuando sea aprobado</li>
                <li>Podrá solicitar enlaces de acceso temporal</li>
            </ol>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'request-access-form']); ?>

            <?= $form->field($model, 'email')->textInput([
                'autofocus' => true,
                'placeholder' => 'ejemplo@gmail.com o usuario@valladolid.tecnm.mx'
            ])->label('📧 Correo Electrónico') ?>

            <?= $form->field($model, 'nombre_completo')->textInput([
                'placeholder' => 'Juan Pérez García'
            ])->label('👤 Nombre Completo') ?>

            <?= $form->field($model, 'departamento')->textInput([
                'placeholder' => 'Sistemas y Computación (opcional)'
            ])->label('🏢 Departamento') ?>

            <div class="form-group">
                <?= Html::submitButton('📤 Enviar Solicitud', [
                    'class' => 'btn btn-request',
                    'name' => 'request-button'
                ]) ?>
            </div>

        <?php ActiveForm::end(); ?>
        
        <div class="back-to-login">
            ¿Ya tienes acceso autorizado? <?= Html::a('Solicitar enlace de acceso', ['site/auth-login']) ?>
        </div>
    </div>
</div>
